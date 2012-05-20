<?php

class Model_DbTable_Utilisateur extends Zend_Db_Table_Abstract
{

    protected $_name = 'utilisateurs';

    protected $_primary		= 'utilisateur_id';
    // protected $_rowClass	= 'Model_Utilisateur';

	static public function getCurrentUser(){
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getStorage()->read();
        if($identity){
            return $identity;
        } else {
            return false;
        }
    } 

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

/*
if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('dev/login/index');
        }
*/
}

