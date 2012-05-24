<?php

class Admin_ApplicationController extends Zend_Controller_Action{
    // protected $_checkActionCookies = true;
    protected $_loggedUser;
  
    public function preDispatch(){
        parent::preDispatch();
        if (!defined('__SITE_URL')) {
        	define('__SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
        }
		$user_model = new Model_Utilisateur();
       	$this->view->loggedUser = $this->_loggedUser = $user_model->getCurrentUser();

		if($this->view->loggedUser->role_id !=  Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector('index', 'index');
		}
		//currency
		$currency = new Zend_Currency('fr_FR');
		Zend_Registry::set('Zend_Currency', $currency);
    }
    
}
