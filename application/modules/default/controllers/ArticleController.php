﻿<?php

require_once('ApplicationController.php');	
class ArticleController extends ApplicationController
{ 
    public function indexAction()
    {
		//$this->_getParam ne fonctionne pas, why ?
		// $this->_request->getParam('id', 0);
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$articles = new Model_Article();
		
		if(isset($params['tableau'])) {
			switch ($params['tableau']) {
				case "tout":
					$query = $articles->getFilteredArticles($params);
					break;
				case "nouveau":
					$query = $articles->getFilteredArticles($params);
					break;
				case "populaire":
					$query = $articles->getFilteredArticles($params);
				break;
			}
		}
		else {
			$query = $articles->getFilteredArticles($params);
		}
		
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
	            $tableau = new Model_Tableau($form->getValues());
	            $mapper  = new Model_TableauMapper();
	            $mapper->save($tableau);
	            return $this->_helper->redirector('index');
	        }
       } else {
            $id = (int)$this->_request->getParam('id', 0);
            if ($id > 0) {
          		$tableau_model = new Model_Tableau();
				$tableaux = new Model_TableauMapper();
				$tableau = $tableaux->find($id,$tableau_model);
				if(!empty($tableau)){ 
					$form->populate($tableau->toArray());
				}
			}

        }
        $this->view->form = $form;
	}
	
	
	public function articleAction ()
	{
		$model_theme = new Model_ThemeMapper();
        $themes = $model_theme->fetchAll();
		foreach ($themes as $theme) {
			$page = Array('label'=>$theme->theme,
						'module'=>true,
						'controller'=>true,
						'action'=>$theme->theme
						);
			$this->_helper->navigation->add($page);
			
		}
	}
 	
	public function createAction(){
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN)){
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

