<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConfig()
    {
        $_config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $_config);

		// debugging
        if($this->_config->debug) {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', 'on');
        }

        return $_config;
    }
    
 
    
    /**
     * Bootstrap autoloader for application resources
     * 
     * @return Zend_Application_Module_Autoloader
     */
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
                'layout' => 'layout',
                'pluginClass' => 'ZFBlog_Layout_Controller_Plugin_Layout'
            )
        );
        $view->addHelperPath(APPLICATION_PATH . '/modules/default/views/helpers', 'Zend_View_Helper');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
    }
     
    protected function _initNavigation(){
        $this->bootstrap('view');
echo "teste";
        $view = $this->getResource('view');
        // $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        //         
        //         $navigation = new Zend_Navigation($config);
        //         $view->navigation($navigation);

		$this->bootstrap('layout');
		
		$layout = $this->getResource('layout');
		$view = $layout->getView();

		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav1');
		$view->menu1 = new Zend_Navigation($pages);
		var_dump($view->menu1);
		$view->navigation($view->menu1);
		 		
		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav2');
		$view->menu2 = new Zend_Navigation($pages);
		 		
		$pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'sidebar');
		$sidebar = new Zend_Navigation($pages);
		$view->sidebar = $sidebar;
		 		
		// 		
		// 		//store sidebar in the bootstrap registry
		return $view->sidebar;
    }

}


