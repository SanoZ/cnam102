<?php

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
 
}

