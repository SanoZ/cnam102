<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected $_config;
	
	protected function _initAutoload()
    {
        $loader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => APPLICATION_PATH));
        
        return $loader;
    }
	
	function _initConfig()
    {
        // config
        $this->_config = new Zend_Config_Ini(APPLICATION_PATH
            . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $this->_config);

        // debugging
        if($this->_config->debug) {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', 'on');
        } 
    }

	protected function _initView()
    {
        // Init the view
        $view = new Zend_View;
        $view->doctype('XHTML1_STRICT');

        // add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
 
        return $view;
    }

    protected function _initDb()
    { 
        if($this->_config->resources->db) {
           $dbAdapter = Zend_Db::factory($this->_config->resources->db->adapter,$this->_config->resources->db->params->toArray());
           Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
           Zend_Registry::set('dbAdapter', $dbAdapter);
        }
	}

	
	
	protected function _initNavigation()
	{
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
		
		
		//store sidebar in the bootstrap registry
		return $sidebar;
	}
	
	protected function _initf()
    {
		//$this->bootstrap('layout');
		//$layout = $this->getResource('layout');
		//$view = $layout->getView();
		
        

        //$view->addHelperPath(APPLICATION_PATH . '/views/helpers');
		
		//$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        //$viewRenderer->setView($view);
		
		
		//print_r($pages);
		//die();
        
		//$this->bootstrap('db');
		
		
		//$test = new App_SubMenu();
		//$mypages = $test->getNavigationEntries('themes');
		//$test->setNavigationEntries();
        
        
        //return $view;
	}
	protected function _initCustomNav()
{
    //$this->bootstrap('navigation');
    //$navigation = $this->getResource('navigation');
	//echo ($navigation);
    // add custom item

    //return $navigation;
}
	
}

