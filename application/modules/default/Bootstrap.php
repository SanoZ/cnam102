<?php
//require_once('Common/Misc/Time.php');

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
 			$tableau_show = new Zend_Controller_Router_Route_Regex(
 			  	   	 	'article/(\d+)',
 			                 array( 'module' => 'default', 'controller' => 'article', 'action' => 'show','route'=>'tableau_show'),
 			                 array( 1 => 'id') ,  'article/%s'
 			             );
 			$router->addRoute('tableau_show', $tableau_show);
 
			$tableau_edit = new Zend_Controller_Router_Route_Regex(
			 	 	'article/edit/(\d+)',
			            array( 'module' => 'default', 'controller' => 'article', 'action' => 'edit','route'=>'tableau_edit'),
			            array( 1 => 'id'),  'article/edit/%s'
			        );
			$router->addRoute('tableau_edit', $tableau_edit);
		
	  	  	$theme_show = new Zend_Controller_Router_Route_Regex(
		  	   	 	'theme/(\d+)',
		                 array( 'module' => 'default', 'controller' => 'theme', 'action' => 'show', 'route'=>'theme_show'),
		                 array( 1 => 'id'),  'theme/%s'
             );
			$router->addRoute('theme_show', $theme_show);
			
	  	  	$theme_edit = new Zend_Controller_Router_Route_Regex(
		  	   	 	'theme/edit/(\d+)',
		                 array( 'module' => 'default', 'controller' => 'theme', 'action' => 'edit', 'route'=>'theme_edit'),
		                 array( 1 => 'id'),  'theme/edit/%s'
             );
			$router->addRoute('theme_edit', $theme_edit);
	 
			  	 
			// 
			$signup = new Zend_Controller_Router_Route(
			    'signup',
			    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'signup','route'=>'signup')
			);
			$router->addRoute('signup', $signup);
			 
			$login = new Zend_Controller_Router_Route(
			    'login',
			    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'index','route'=>'login')
			);
			$router->addRoute('login', $login);
			$accueil = new Zend_Controller_Router_Route(
			    'accueil',
			    array( 'module' => 'default', 'controller' => 'index', 'action' => 'index','route'=>'accueil')
			);
			$router->addRoute('accueil', $accueil);
			
			$logout = new Zend_Controller_Router_Route_Regex(
			    'logout',
			    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'logout','route'=>'logout')
			);
			$router->addRoute('logout', $logout);
			
			$article_tout = new Zend_Controller_Router_Route(
			    'article/tout',
			    array( 'module' => 'default', 'controller' => 'article', 'action' => 'index','tableau'=>'tout',
						'route'=>'article_tout')
			);
			$router->addRoute('accueil', $article_tout);
			
			$article_nouveau = new Zend_Controller_Router_Route(
			    'article/nouveau',
			    array( 'module' => 'default', 'controller' => 'article', 'action' => 'index','tableau'=>'nouveau',
						'route'=>'article_nouveau')
			);
			$router->addRoute('article_nouveau', $article_nouveau);
			
			$article_populaire = new Zend_Controller_Router_Route(
			    'article/populaire',
			    array( 'module' => 'default', 'controller' => 'article', 'action' => 'index','tableau'=>'populaire',
						'route'=>'article_populaire')
			);
			$router->addRoute('article_populaire', $article_populaire);
	}
    
    private function loadStaticRouter(){
        // $fc = Zend_Controller_Front::getInstance();
        //        $router = $fc->getRouter();
        //  
    }
  

}

