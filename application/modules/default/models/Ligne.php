<?php

class Model_Ligne {
	
	public function getOrderLinesByOrderId($id, $rename=false) {
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		if ($rename) {
			$select->from(
			array('ligne'),
			array('id'=>'ligne_id')
			);
		}
		else {
			$select->from('ligne');
		}
		$select->where("commande_id = ?", $id);
		return $select;
	}
}