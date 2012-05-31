<?php

class Model_Article {
	
	public function find($id){
		if(is_int($id)){
			
			$res_array = array();
			$select = Zend_Db_Table::getDefaultAdapter()->select();
			$select->from('articles');
			$select->where("article_id = ?" ,$id); 
			$res = $select->query();  
			foreach ($res as $val => $article){ 
				$res_array [$val] = $article;
			}
			return $res_array;	
		}
		return false;
	}
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