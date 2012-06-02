<?php

Class Model_DbTable_Ligne extends Zend_Db_Table_Abstract
{
	protected $_name = 'ligne_panier'; 
    protected $_primary		= array( 'article_id', 'panier_id');
	
 
	public function getOrderLinesByOrderId($id, $rename=false) {
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		if ($rename) {
			$select->from( array('ligne'), array('id'=>'ligne_id') );
		}
		else {
			$select->from('ligne');
		}
		$select->where("commande_id = ?", $id);
		return $select;
	}
}