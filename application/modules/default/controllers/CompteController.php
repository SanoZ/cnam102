<?php

require_once('ApplicationController.php');

class CompteController extends ApplicationController
{
	// public $_loggedUser;
    public function init(){
        /* Initialize action controller here */
		if($this->_loggedUser){
			$this->_helper->redirector('signup', 'auth');
		}
		//si requete ajax on désactive les layouts
        if($this->_request->isXmlHttpRequest()){
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

	public function indexAction(){
        $form = new Form_Registration();
        $this->view->form = $form;
		$request = $this->getRequest();
		if ($request->isPost()) { 
			if ($form->isValid($request->getPost())) {
				$user = new Model_Utilisateur();
				try{
					$data = $form->getValues();
					$data["utilisateur_id"] = $this->_loggedUser->utilisateur_id ;
					if ( $user->update($data) ) {
						$this->view->message = "Vos données ont été mis à jour.";
					}else{
						$this->view->message = "Erreur, Vérifiez votre email et/ou mot de passe;";
					}
				}catch(Exception $e){
					$this->view->errorMessage = $e->getMessage();
				}
			}
		}
		$this->view->form = $form;
	}

	
	public function forgotPasswordAction(){

        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $user_model = new Model_User();
        $global_model = new Model_Globals();

        $result = array();
        $email = $this->_request->getParam('email_forgot',"");
        
        $result['text'] = "<p>The email entered is invalid</p>";
        if($email != ''){
            $user = $user_model->getUserByEmail($email);
            if($user != null){
                //generating the new password
                $user_model->loadUser($user['user_id']);
                $pass = $global_model->genRandomString(10);
                $user_model->user_password =  md5($pass);

                //$this->sendEmailForgotPasword($email, $pass);
                $model_email = new Model_Email();
                $model_email->sendEmailForgotPasword($email,$pass);

                $result['text'] = "<p class='register-success'>Your password has been sent out.<br/>Please check your email.</p>";
            } else {
                $result['text'] = "<p>There in no user with this email account.</p>";
            }
        } 
        $this->_helper->json->sendJson($result);
    }
 

 	public function commandeAction()
	{
		$params=Zend_Controller_Front::getInstance()->getRequest()->getParams();
	
		$commandes = new Model_Commande();
		$query = $commandes->getOrdersByCustomerId('1'); //à remplacer par le session_id du client
	
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($query));
		$paginator->setItemCountPerPage(10)
				  ->setCurrentPageNumber(isset($params['page']) ? $params['page'] : 1);
		$this->view->paginator = $paginator;
	}
	
	public function ligneAction()
	{	
		$commande=Zend_Json::decode($this->_request->getParam('commande'));
		$id=$commande['commande_id'];
		$lignes = new Model_Ligne();
		$select = $lignes->getOrderLinesByOrderId($id);
		$results = $select->query()->fetchAll();
		$this->view->results=$results;
		$this->view->commande=$commande;

	}
	
	public function pdfAction()
	{
		$id=$this->_request->getParam('id');
		$commandes = new Model_Commande();
		$lstCommandes = $commandes->getOrderById($id,true)->query()->fetchAll();
		
		// constructeur, création d'un document XML
		$docXML = new DomDocument('1.0', 'utf-8');
		$panierCommandes = $docXML->createElement('PanierCommandes');
		$panierCommandes = $docXML->appendChild($panierCommandes);
		
		foreach ($lstCommandes as $elmCde) {
			$commande = $docXML->createElement('Commande');
			$commande = $panierCommandes->appendChild($commande);
			foreach ($elmCde as $k=>$v) {
				$commande->setAttribute($k,$v);
			}
			$panierLignes = $docXML->createElement('PanierLignes');
			$panierLignes = $commande->appendChild($panierLignes);
			$lignes = new Model_Ligne();
			$lstLignes = $lignes->getOrderLinesByOrderId($elmCde['id'])->query()->fetchAll();
			foreach ($lstLignes as $elmLig) {
				$ligne = $docXML->createElement('Ligne');
				$ligne = $panierLignes->appendChild($ligne);
				foreach ($elmLig as $k=>$v) {
					$ligne->setAttribute($k,$v);
				}
			}
		}
		//$docXML->validate();
		
		$xmlFolder = realpath(APPLICATION_PATH . '/../public/xml/');
		$xmlFolder = str_replace("\\","/",$xmlFolder);
		$filename = "commande_".$elmCde['id'];
		$docXML->save($xmlFolder."/output/".$filename.".xml");
		
		$fopPath = "\"C:/Documents and Settings/Utilisateur/Bureau/exemple xsl/fop-1.0/fop\"";
		$cmd = "-xml ".$xmlFolder."/output/".$filename.".xml -xsl ".$xmlFolder."/input/Commande.xsl -pdf ".$xmlFolder."/output/".$filename.".pdf";
		$cmd = $fopPath." ".$cmd." 2>&1";
		exec($cmd,$ret);
		print_r($ret);
		//die();
		$this->view->folder=$xmlFolder;
		$this->view->filename=$filename.".pdf";
	}


 }

