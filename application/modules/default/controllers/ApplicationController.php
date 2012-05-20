<?php

class ApplicationController extends Zend_Controller_Action{
    // protected $_checkActionCookies = true;
    protected $_loggedUser;
  
    public function preDispatch(){
        parent::preDispatch();
         
        if (!defined('__SITE_URL')) {
        	define('__SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
        }
       	$this->view->loggedUser = $this->_loggedUser = Model_DbTable_Utilisateur::getCurrentUser();
    }
    
}
