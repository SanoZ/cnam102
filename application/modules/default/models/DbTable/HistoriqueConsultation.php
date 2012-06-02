<?php

class Model_DbTable_HistoriqueConsultation extends Zend_Db_Table_Abstract
{

    protected $_name = 'historique_consultation';
    protected $_primary	= array('session_id','date');

	//on initialise pas les variables passées dans la fonction pour forcer le passage de valeur
	//une erreur sera renvoyé dans l'autre cas.
 	static public function ajoutConsultation($session_id, $article_id){ 
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert('historique_consultation',array(
		                        'session_id' => $session_id,
		                        'date'=> date("Y-m-d H:i:s"),
		                        'article_id'   => $article_id));
	}
	
	//retourne un objet 
	static public function getMostviewedPages($limit = false, $paginator =false){
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from(array('hc'=>"historique_consultation", )); 
		$select->join(array('a' => 'articles'), 'a.article_id = hc.article_id',array('nb_viewed' => 'COUNT(*)'));
		$select->group("article_id");
		
		if($limit){
			$select->limit($limit);
		}
		if(!$paginator){ 
		 	$res = $select->query(); 
			return $res;
		}
		return $select;
	}
}

