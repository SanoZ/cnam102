<?php

class Model_Commande {
	
	public function getOrdersByCustomerId($id) {
		
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		$select->from('commande');
		$select->where("utilisateur_id = ?", $id);
		return $select;
	}
	
	public function getOrderById($id, $rename=false) {
		$select = Zend_Db_Table::getDefaultAdapter()->select();
		if ($rename) {
			$select->from(
				array('commande'),
				array('id' => 'commande_id', 'date'=>'date', 'client_id'=>'utilisateur_id')
				);
		}
		else {
			$select->from('commande');
		}
		$select->where("commande.commande_id = ?", $id);
		return $select;
	}
}
