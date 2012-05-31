<?php

class Model_Article {
	
	public function getFilteredArticles(array $params, $paginator = true) { 
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('articles'); 
		
	 	foreach($params as $key => $value) {
			if(!empty($value)) {
				switch($key) {
					case "theme":
						if($value != "tout"){
							$select->where("theme_id = ?", $value);
						}
						break;
					case "dimension": 
						$select->where("dimension_id = ?", $value);
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
	
}