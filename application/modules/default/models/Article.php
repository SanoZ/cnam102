<?php

class Model_Article {
	
	/*public function getArticles() {
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('articles');
		return $select;
	}*/
	
	public function getFilteredArticles() {
		$whereArray=array();
		$params=Zend_Controller_Front::getInstance()->getRequest()->getParams();
		if(is_array($params)){
			foreach($params as $key=>$value) {
				if($value!="tout") {
					switch($key) {
						case "theme":
							$whereArray['theme_id']=$value;
							break;
						case "dimension":
							$whereArray['dimension_id']=$value;
							break;
					}
				}
			}
		}
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('articles');
		foreach($whereArray as $key=>$value) {
			$select->where("{$key} = ?", $value);
		}
		return $select;
	}
	
}