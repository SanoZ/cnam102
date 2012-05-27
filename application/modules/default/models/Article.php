<?php

class Model_Article {
	
	public function getFilteredArticles($params) {
		$whereArray = array();
		
		if(is_array($params)){
			foreach($params as $key => $value) {
				
				if($value != "tout") {
					
					switch($key) {
						case "theme":
							$whereArray['theme_id'] = $value;
							break;
						case "dimension":
							$whereArray['dimension_id'] = $value;
							break;
					}
				}
			}
		}
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('articles');
		
		foreach($whereArray as $key => $value) {
			$select->where("{$key} = ?", $value);
		}
		return $select;
	}
	
}