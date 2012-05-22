<?php

require_once('ApplicationController.php');	
class ArticleController extends ApplicationController
{

    public function init()
    {
        /* Initialize action controller here */
		//$uri = $this->request->getPathinfo();
		//$activeNav = $this->view->navigation()->findByUri($uri);
		//$activeNav->active=true;
    }

    public function indexAction()
    {
		//$this->_getParam ne fonctionne pas, why ?
		$params=Zend_Controller_Front::getInstance()->getRequest()->getParams();
		
		$articles = new Frontend_Model_Article();
		
		if(isset($params['tableau'])) {
			switch ($params['tableau']) {
				case "tout":
					$query = $articles->getFilteredArticles();
					break;
				case "nouveau":
					$query = $articles->getFilteredArticles();
					break;
				case "populaire":
					$query = $articles->getFilteredArticles();
				break;
			}
		}
		else {
			$query = $articles->getFilteredArticles();
		}
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($query));
		$paginator->setItemCountPerPage(3)
				  ->setCurrentPageNumber(isset($params['page']) ? $params['page'] : 1);
		$this->view->paginator = $paginator;
		
    }
	
	public function articleAction ()
	{
		$model_theme = new Frontend_Model_DbTable_Theme();
        $themes = $model_theme->getTheme();
		foreach ($themes as $theme) {
			$page=Array('label'=>$theme['nom'],
						'module'=>true,
						'controller'=>true,
						'action'=>$theme['nom']
						);
			$this->_helper->navigation->add($page);
			
		}
	}
// ?????????????????
	// public function editAction()
	// {
	// 	$form = new Application_Form_Album();
	//        	$form->submit->setLabel('Save');
	// 	$this->view->form = $form;
	// 	if ($this->getRequest()->isPost()) {
	// 	      $formData = $this->getRequest()->getPost();
	// 	      if ($form->isValid($formData)) {
	// 	             $id = (int)$form->getValue('id');
	// 	             $artist = $form->getValue('artist');
	// 	             $title = $form->getValue('title');
	// 	             $albums = new Application_Model_DbTable_Albums();
	// 	             $albums->updateAlbum($id, $artist, $title);
	// 	             $this->_helper->redirector('index');
	// 	      } else {
	// 	             $form->populate($formData);
	// 	} else {
	// 	        $id = $this->_getParam('id', 0);
	// 	        if ($id > 0) {
	// 	}
	// } 
}

