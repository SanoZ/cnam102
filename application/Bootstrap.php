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
			// echo "debug";
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', 'on');
		} 
		//currency
		$currency = new Zend_Currency('fr_FR');
		Zend_Registry::set('Zend_Currency', $currency);
	}
	
	protected function _initSession() {
		// On initialise la session
		$session = new Zend_Session_Namespace ( 'ecommerce', true );
		Zend_Registry::set('session',$session);
		if(!isset($session->panier)){
			$session->panier =  new App_Panier_Panier();
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
    }
       
    protected function _initDoctype() {
        
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');
		$layout = explode('/', $_SERVER['REQUEST_URI']);

		
        Zend_Layout::startMvc(
            array(
                'layoutPath' => APPLICATION_PATH . "/layouts/scripts",
                'layout' => 'layout',
                // 'pluginClass' => 'Common_Layout_Controller_Plugin_Layout'
            )
        );

        $view->addHelperPath(APPLICATION_PATH . '/modules/default/views/helpers', 'Zend_View_Helper');
        // $view->addHelperPath(APPLICATION_PATH . '/../library/ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
    }
/*
$this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        Zend_Layout::startMvc(
            array(
                'layoutPath' => APPLICATION_PATH . "/layouts/scripts",
                'layout' => 'layout',
                'pluginClass' => 'ZFBlog_Layout_Controller_Plugin_Layout'
            )
        );
*/
	
	protected function _initPanier(){
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
	 	$view = $layout->getView();
	
		// 	//qteArticle
		Zend_Session::start();
		$session = Zend_Registry::get('session');
		$panier =  $session->panier;
        $view->qteArticle = sizeof($panier->getLignes());
	}
	
	protected function _initNavigation(){
		$this->bootstrap('view');
		$this->bootstrap('layout');
	 		
		$layout = $this->getResource('layout');
	 	$view = $layout->getView();
			
		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'base');
	 	$view->base = new Zend_Navigation($pages);
	 		
	 	$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'loggedin');
		$view->loggedin = new Zend_Navigation($pages);
		
 		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'loggedout');
		$view->loggedout = new Zend_Navigation($pages);
			
		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'sidebar');
		$view->sidebar = new Zend_Navigation($pages);
		
 		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'admin');
		$view->admin = new Zend_Navigation($pages);
		 
	}
 
}

