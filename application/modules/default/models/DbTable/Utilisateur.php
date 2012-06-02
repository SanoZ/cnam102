<?php

class Model_DbTable_Utilisateur extends Zend_Db_Table_Abstract
{

    protected $_name = 'utilisateurs';
    protected $_primary		= 'utilisateur_id';
    // protected $_rowClass	= 'Model_Utilisateur';



    static public function getUserByEmail($email ){
		$user = array();
		$table = new Model_DbTable_Utilisateur();
        $user = $table->fetchRow("email like '%".$email."%'");
		return $user; 
    }

 	static public function getUserById($user_id){
		$table = new Model_DbTable_Utilisateur();
        $user = $table->fetchRow("utilisateur_id = '{$user_id}'");
        if ($user){
            return $user;
        }
        return false;
    }

	static public function isEmailRegistered($email){
		return false;
	}

}

