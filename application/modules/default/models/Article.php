<?php 
class Model_Article {
	
	/* Return Array
	*/
	public function find($id){ 
		if( $id){
			
			$res_array = array();
			$select = Zend_Db_Table::getDefaultAdapter()->select();
			$select->from('articles');
			$select->where("article_id = ?" ,$id); 
			$res = $select->query();  
			
			foreach ($res as $val => $article){ 
				$res_array  = $article;
			}
			return $res_array;	
		} 
		return false;
	}
	
	/*
	* Return 
	* -
	*/
	public function getFilteredArticles(array $params, $paginator = true) { 
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from(array('a' => 'articles')); 
		$select->where("stock = 1 and active=1");
		
	 	foreach($params as $key => $value) {
			if(!empty($value)) {
				switch($value) {
					case "theme":
						if($value != "tout"){
							$select->where("theme_id = ?", $value);
						}
						break;
					case "dimension": 
						$select->where("dimension_id = ?", $value);
						break;
					case "nouveau":
						$todayDate = date("Y-m-d");  // current date
						$dateOneMonthAgo = strtotime(date("Y-m-d", strtotime($todayDate)) . "-1 month"); //Add one day to today
					 	$select->where("date_publication >= ?",date('Y-m-d', $dateOneMonthAgo) );
						$select->order(array("date_publication desc" ));
						break; 
					case "populaire": 
						$select->join(array('hc'=>"historique_consultation"), 'a.article_id = hc.article_id', array('nb_viewed' => 'COUNT(*)'));
						$select->group("hc.article_id");
  
						break;
				}
			}
		} 
		
		if(!$paginator){ 
		 	$res = $select->query(); 
			return $res;
		} 
		return $select;
	}
	
	public function getArticles($paginator = true) { 
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('articles'); 
	 
		if(!$paginator){ 
		 	$res = $select->query(); 
			return $res;
		}
		return $select;
	}
	
	public function searchArticles($search, $paginator = true){
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('articles');
		$select->where("description like ? or title like ?" , $search, $search);
		
		if(!$paginator){ 
		 	$res = $select->query(); 
			return $res;
		}
		return $select;
		  
	}
	public function save($tableau)
    {  
        $data = array(
            'titre'   => $tableau['titre'],
            'description' => $tableau['description'],
            'theme_id' => $tableau['theme_id'],
            'date_publication' => date('Y-m-d H:i:s'),
            'date_modification' => date('Y-m-d H:i:s'),
            'stock' => 1,
            'active' =>  $tableau['active'],
            'image' =>  $tableau['image']
        ); 
	
	$table = Zend_Db_Table::getDefaultAdapter();
        if (null === ($id = $tableau['article_id'] ) ) {
            unset($data['article_id']);
			$where = $table->getAdapter()->find('article_id = ?', $data['article_id']);
            $table->insert($data,$data['article_id']);
        } else {
            $table->update($data, array('article_id = ?' => $id));
        }
    }
	
}