<?php

class Default_ArticleController extends Zend_Controller_Action
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
}

