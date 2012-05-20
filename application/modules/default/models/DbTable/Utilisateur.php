<?php

class Model_DbTable_Utilisateur extends Zend_Db_Table_Abstract
{

    protected $_name = 'utilisateurs';

    protected $_primary		= 'utilisateur_id';
    protected $_rowClass	= 'Model_Utilisateur';

	static public function getCurrentUser(){
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getStorage()->read();
        if($identity){
            return $identity;
        } else {
            return false;
        }
    } 


/*
if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('dev/login/index');
        }
*/
}

