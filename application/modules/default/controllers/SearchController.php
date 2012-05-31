<?php
// require_once 'Zend/Search/Lucene.php';

require_once('ApplicationController.php');
class SearchController extends ApplicationController
{
	
  public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
 		$search = trim($this->_getParam('search'));

		$article_model = new Model_Article();
		if (!empty($query)){ 
			$articles = $article_model->searchArticles($search);
		}else{
			$articles = $article_model->getArticles();
		}
    	$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($articles));
		$paginator->setItemCountPerPage(3)
				  ->setCurrentPageNumber(isset($params['page']) ? $params['page'] : 1);
		$this->view->paginator = $paginator;
	} 

    

    // public function searchAction() {
    //         $query = $this->_getParam('search');
    //         $index = Zend_Search_Lucene::open(APPLICATION_PATH.'/../product-index');
    //         $hits = $index->find($query);
    //         $adapter = new Zend_Paginator_Adapter_Array($hits);
    //         $paging = new Zend_Paginator($adapter);
    //         $paging->setItemCountPerPage(6);
    //         $page = $this->_request->getParam('page', 1);
    //         $paging->setCurrentPageNumber($page);
    //         $this->view->hits = $paging; 
    //     }
}