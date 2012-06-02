<?php
require_once('ApplicationController.php');	
class ArticleController extends ApplicationController
{ 
	
	public function preDispatch(){
        parent::preDispatch(); 
		
	}
	
	public function showAction(){
		
		$article_id = $this->_request->getParam('id', ""); 
		$model_article = new Model_Article();
		$this->view->article = $model_article->find($article_id);
		if(empty($this->view->article)){
			$this->_helper->redirector("index");
			exit;
		} 
		try{
			//on met a jour la table historique
			Model_DbTable_HistoriqueConsultation::ajoutConsultation( Zend_Session::getId(),$article_id);
		}catch(Exception $e){
			//TODO -> loguer cette erreur dans le fichier de log.
		}
	}
    public function indexAction()
    { 
		$params = array();
		$params['tableau'] =  $this->_request->getParam('tableau', "");
		$params['theme']  =  $this->_request->getParam('theme', "");

		$articles = new Model_Article(); 
		$query = $articles->getFilteredArticles( $params);
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($query));
		$paginator->setItemCountPerPage(3)
				  ->setCurrentPageNumber(isset($params['page']) ? $params['page'] : 1);
		$this->view->paginator = $paginator;
		
    }

	public function editAction(){
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector("index");
			exit;
		}
		
		$request = $this->getRequest();
        $form    = new Form_Tableau();


        if ($this->getRequest()->isPost()) {  
        	if ($form->isValid($request->getPost())) { 
	            $tableau = new Model_Article(); 
	            $tableau->save($form->getValues());
	            return $this->_helper->redirector('index');
	        } 
       } else { 
           $id = (int)$this->_request->getParam('id', 0); 
           if ($id > 0) {
          		$tableau_model = new Model_Article(); 
				$tableau = $tableau_model->find($id );   
				if(!empty($tableau)){ 
					$form->populate($tableau);
				}else{ 
					$this->_helper->redirector("create");
				}
			}else{ 
				$this->_helper->redirector("create");
			} 
        }
        $this->view->form = $form;
	}
	
	
	// public function articleAction ()
	// {
	// 	$model_theme = new Model_ThemeMapper();
	//         $themes = $model_theme->fetchAll();
	// 	foreach ($themes as $theme) {
	// 		$page = Array('label'=>$theme->theme,
	// 					'module'=>true,
	// 					'controller'=>true,
	// 					'action'=>$theme->theme
	// 					);
	// 		$this->_helper->navigation->add($page);
	// 		
	// 	}
	// }
 	
	public function createAction(){
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector('index'); 
			exit;
		}
        $request = $this->getRequest();
        $form    = new Form_Tableau();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) { 
	
                $tableau = new Model_Tableau($form->getValues());
				if ($form->image->isUploaded()) {
					$mapper  = new Model_TableauMapper();
					$mapper->save($tableau);	
	                return $this->_helper->redirector('index'); 
                
				}else{
				    echo "Erreur pendant l'upload de l'image";
				}
            }
        }

        $this->view->form = $form;
	}
}

