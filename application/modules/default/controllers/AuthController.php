<?php
require_once('ApplicationController.php');

class AuthController extends ApplicationController
{
	public function init() {
  }

	public function indexAction()
	{
		if($this->_loggedUser){
			$this->_helper->redirector('index', 'index');
		}
		$form = new Form_Login();
		$this->view->message = "";
		$request = $this->getRequest();
		if ($request->isPost()) { 
			if ($form->isValid($request->getPost())) {
				if ($this->_process($form->getValues())) {
					// We're authenticated! Redirect to the home page
					$this->_helper->redirector('index', 'index');
				}else{
					$this->view->message = "Erreur, Vérifiez votre email et/ou mot de passe;";
				}
			}
		}
		$this->view->form = $form;
	}
	
	protected function _process($values)
	{
     // Get our authentication adapter and check credentials
     $adapter = $this->_getAuthAdapter();
     $adapter->setIdentity($values['mail']); 
     $adapter->setCredential(sha1($values['password']));

     $auth = Zend_Auth::getInstance();
     $result = $auth->authenticate($adapter);
     if ($result->isValid()) {
         $user = $adapter->getResultRowObject();
         $auth->getStorage()->write($user);
         return true;
     }
     return false;
	}
	
	protected function _getAuthAdapter() {

		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

		$authAdapter->setTableName('utilisateurs')
		    ->setIdentityColumn('email')
		    ->setCredentialColumn('password');
		    // ->setCredentialTreatment('SHA1(CONCAT(?,salt))');
 
		return $authAdapter;
	}
	
	public function signupAction()
    {
		if($this->_loggedUser){
			$this->_helper->redirector('index', 'index');
		}
        
        $form = new Form_Registration();
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                if($data['password'] != $data['confirmPassword']){
                    $this->view->errorMessage = "Password et confirmation sont différents.";
                    return;
                }
                if(Model_DbTable_Utilisateur::isEmailRegistered($data['email'])){
                    $this->view->errorMessage = "Cet email existe déjà dans notre base.";                                                                                                  
					return;                                                                                                                                       				
				}
                unset($data['confirmPassword']);
				$user = new Model_UtilisateurMapper();
                $user->insert($data);
                $this->_redirect('/auth');
            }
        }
		$this->view->errorMessage = "Erreur. Veuillez essayer à nouveau."; 
    }
	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index'); // back to login page
	}
}