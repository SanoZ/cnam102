<?php

class App_Panier_Ligne {
	
	protected $_article;
    protected $_qteArticle;
	
	public function  __construct($articleId) {
				        $this->_qteArticle = 0;
				        // $articles = new Model_DbTable_Article();
				        // 				        $article = $articles->fetchRow(array('id = ?'=>$articleId))->toArray();
				        // 				        $this->_article = $article;
				    }
					
					public function ajouter($qte = 1 ) {
				        $this->_qteArticle = $qte;
				    }
				    
				    public function getArticle() {
				        return $this->_article;
				    }
				
				    public function setArticle($article) {
				        $this->_article = $article;
				    }
				
				    public function getQteArticle() {
				        return $this->_qteArticle;
				    }
				
				    public function setQteArticle($qteArticle) {
				        $this->_qteArticle = $qteArticle;
				    }


}