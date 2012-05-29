<?php
class WebService {
  	/**
		 * Addition de 2 entiers
		 * @param integer $a
		 * @param integer $b
		 * @return integer
		 */
	public function add($date, $info) {
		$params['date'] = $date;
		$params['theme'] = $info; 
		
		$articles = new Model_Article();
		$result = $articles->getFilteredArticles($params, true);
		if (count($result) != 1) {        
		  	throw new Exception('Invalid product ID: ' . $id);  
		} 
		return $result;
	}
 
}
?>