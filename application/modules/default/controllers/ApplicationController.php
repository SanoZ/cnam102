<?php

class ApplicationController extends Zend_Controller_Action{
    // protected $_checkActionCookies = true;
    protected $_loggedUser;
	protected $_siteweb = "ventes.com";
  
    public function preDispatch(){
        parent::preDispatch();
        if (!defined('__SITE_URL')) {
        	define('__SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
        }
		$user_model = new Model_Utilisateur();
       	$this->view->loggedUser = $this->_loggedUser = $this->getCurrentUser();
 
		//currency
		$currency = new Zend_Currency('fr_FR');
		Zend_Registry::set('Zend_Currency', $currency);
    }

	protected function getCurrentUser(){
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getStorage()->read();
		if($identity){
		    return $identity;
		} else {
		    return false;
		}
	}
 
	protected function _initPanier(){
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
	 	$view = $layout->getView();
	 
		Zend_Session::start();
		$session = Zend_Registry::get('session');
		if(!isset($session->panier)){
			$session->panier = new Model_Panier();
		}
		$panier =  $session->panier;
		$this->view->qteArticle = sizeof($panier->getLignes());
	}
	
	
	protected function getPanier() {
        // $session = Zend_Registry::get('session');
        // return $session->panier;
    }
    
}
