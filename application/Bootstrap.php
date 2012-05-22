<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected $_config;
 
	
	function _initConfig(){
		// config
		$this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		Zend_Registry::set('config', $this->_config);
									
		// debugging
		if($this->_config->debug) {
			echo "debug";
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', 'on');
		} 
	}
	
	protected function _initAutoload() {
        
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH . '/modules/default' ,
        ));
        
        
        $acl = new Model_Acl();
        $auth = Zend_Auth::getInstance();
        $authNamespace = new Zend_Session_Namespace('Zend_Auth');
        $authNamespace->setExpirationSeconds(28800);
        
        $fc = Zend_Controller_Front::getInstance();
        
        $fc->registerPlugin(new Plugin_AccessCheck($acl, $auth));
        $fc->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
                    'module' => '',
                    'controller' => 'error',
                    'action' => 'error')));
        
        return $autoloader;
    }
       
    protected function _initDoctype() {
        
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

        Zend_Layout::startMvc(
            array(
                'layoutPath' => APPLICATION_PATH . "/layouts/scripts",
                'layout' => 'layout'
            )
        );
        $view->addHelperPath(APPLICATION_PATH . '/modules/default/views/helpers', 'Zend_View_Helper');
        $view->addHelperPath(APPLICATION_PATH . '/../library/ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'ViewRenderer'
        );
        $viewRenderer->setView($view);
		

    }
 

 	protected function _initDb(){ 
 	    if($this->_config->resources->db) {
 	   		$dbAdapter = Zend_Db::factory( $this->_config->resources->db->adapter,$this->_config->resources->db->params->toArray());
 	   	    Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
 	   	    Zend_Registry::set('dbAdapter', $dbAdapter);
 	   	}
	}

	protected function _initSession() {
		// On initialise la session
		$session = new Zend_Session_Namespace ( 'ecommerce', true );
		Zend_Registry::set('session',$session);
		if(!isset($session->panier)){
			$session->panier =  new App_Panier_Panier();
		}
		return $session;
	}

	
	protected function _initNavigation(){
			//execute after db
	 	$this->bootstrap('db');
	 		
		$this->bootstrap('view');
		$this->bootstrap('layout');
	 		
		$layout = $this->getResource('layout');
	 	$view = $layout->getView();
			
		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav1');
	 	$view->menu1 = new Zend_Navigation($pages);
	 		
	 	$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav2');
		$view->menu2 = new Zend_Navigation($pages);
			
		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'sidebar');
		$sidebar = new Zend_Navigation($pages);
		$view->sidebar = $sidebar;
			
		//currency
		$currency = new Zend_Currency('fr_FR');
		Zend_Registry::set('Zend_Currency', $currency);
		// 
		// 	//qteArticle
		Zend_Session::start();
		$session = Zend_Registry::get('session');
		$panier =  $session->panier;
        $view->qteArticle = sizeof($panier->getLignes());
				
		//store sidebar in the bootstrap registry
		return $view->sidebar;
	}
 
}

