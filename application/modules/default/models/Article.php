<?php

class Model_Article {
	
	public function getFilteredArticles(array $params, $paginator= false) { 
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('articles'); 
		
			foreach($params as $key => $value) {
				
				if(!empty($value)) {
					switch($key) {
						case "theme": 
							$select->where("theme_id = ?", $value);
							break;
						case "dimension":echo "dimension".$value;
								$select->where("dimension_id = ?", $value);
							break;
					}
				}
			}
			if(isset($params['date'])){
				$select->where("date_publication >= ?", $params['date']);
			} 
		if($paginator){
			return $select->query();
		}
		return $select;
	}
	
}