<?php
require_once('Common/Misc/Time.php');

class Default_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Bootstrap the view doctype
     *
     * @return void
     */
    protected function _initRouter() {
		$this->loadDefaultRouter();
        
        //mapping of the static routes       
        $this->loadStaticRouter();
    }
  
	private function loadDefaultRouter(){
		$fc = Zend_Controller_Front::getInstance();
		$router = $fc->getRouter();
 		// 
		$rss = new Zend_Controller_Router_Route_Regex(
  	   	 	'/rss',
                 array( 'module' => 'default', 'controller' => 'rss', 'action' => 'index') 
             );
		$router->addRoute('rss', $rss);
 	
 		$tableau_edit = new Zend_Controller_Router_Route_Regex(
  	   	 	'/tableau/edit/(.*)',
                 array( 'module' => 'default', 'controller' => 'tableau', 'action' => 'edit'),
                 array( 1 => 'id')
             );
		$router->addRoute('tableau_edit', $tableau_edit);
		
  	  	$theme_edit = new Zend_Controller_Router_Route_Regex(
  	   	 	'/theme/edit/(.*)',
                 array( 'module' => 'default', 'controller' => 'theme', 'action' => 'edit'),
                 array( 1 => 'id')
             );
		$router->addRoute('theme_edit', $theme_edit);
	
		
		$signup = new Zend_Controller_Router_Route_Regex(
		    'signup',
		    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'signup')
		);
		$router->addRoute('signup', $signup);
		 
		$login = new Zend_Controller_Router_Route_Regex(
		    'login',
		    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'index')
		);
		$router->addRoute('login', $login);
		
		$logout = new Zend_Controller_Router_Route_Regex(
		    'logout',
		    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'logout')
		);
		$router->addRoute('logout', $logout);
		
	}
    
    private function loadStaticRouter(){
        $fc = Zend_Controller_Front::getInstance();
        $router = $fc->getRouter();

        /*search internal controller*/
        $route = new Zend_Controller_Router_Route_Static(
            "isearch",
            array('module' => 'default', 'controller' => 'search', 'action' => 'index')
        );
        $router->addRoute("isearch", $route);
    }
  

}

