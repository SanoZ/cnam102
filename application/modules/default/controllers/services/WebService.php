<?php  
 
class WebService  {
  		/**
		 * Retourne la liste des tableaux
		 * @return object
		 */
		public function getAllTableaux() {
			$db = Zend_Registry::get('Zend_Db');        
		    $sql = "SELECT * FROM articles";    
		return $db->fetchAll($sql); 
	
		 
	}
 
}
?>