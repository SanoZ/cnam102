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
        
        //mapping of the people routes
        $this->loadPeopleRouter();

		$this->loadDefaultRouter();
        
        //mapping of the static routes       
        $this->loadStaticRouter();
    }
 
	
     
    private function loadPeopleRouter()
    {
        $fc = Zend_Controller_Front::getInstance();
        $router = $fc->getRouter();
        
        // Person page
        $route = new Zend_Controller_Router_Route_Regex(
            'people/(\d+)',
            array( 'module' => 'default', 'controller' => 'people', 'action' => 'profile'),
            array( 1 => 'primary-id'), 
            "people/%d"
        );
        $router->addRoute('person-profile', $route);
        
  
        /**
         * Friendly URLs 
         */
        $route = new Zend_Controller_Router_Route_Regex(
            'user/(.*)',
            array( 'module' => 'default', 'controller' => 'people', 'action' => 'profile'),
            array( 1 => 'primary-id')
        );
        $router->addRoute('person-profile-friendly', $route);
     
    }	
	
	private function loadDefaultRouter(){
		$fc = Zend_Controller_Front::getInstance();
		$router = $fc->getRouter();
 
		$route = new Zend_Controller_Router_Route_Regex(
		    'signup',
		    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'signup')
		);
		$router->addRoute('signup', $route);
		 
		$route = new Zend_Controller_Router_Route_Regex(
		    'login',
		    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'index')
		);
		$router->addRoute('login', $route);
		
		$route = new Zend_Controller_Router_Route_Regex(
		    'logout',
		    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'logout')
		);
		$router->addRoute('logout', $route);
		
	}
    
    private function loadStaticRouter(){
        $fc = Zend_Controller_Front::getInstance();
        $router = $fc->getRouter();
        //about-us
        $route = new Zend_Controller_Router_Route_Static(
            'about-us',
            array('module' => 'default', 'controller' => 'topic', 'action' => 'index', 'primary-name' =>'about-us')
        );
        $router->addRoute('about-us', $route);   
        
        
        //site map
        $route = new Zend_Controller_Router_Route_Static(
            'sitemap',
            array('module' => 'default', 'controller' => 'sitemap', 'action' => 'index')
        );
        $router->addRoute('sitemap', $route);        
        
        /*search internal controller*/
        $route = new Zend_Controller_Router_Route_Static(
            "isearch",
            array('module' => 'default', 'controller' => 'search', 'action' => 'index')
        );
        $router->addRoute("isearch", $route);
    }
  

}

