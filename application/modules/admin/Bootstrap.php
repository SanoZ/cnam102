<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    
    public function _initRouter(){
		$fc = Zend_Controller_Front::getInstance();
        $router = $fc->getRouter();
		$route = new Zend_Controller_Router_Route_Regex(
		    'login',
		    array( 'module' => 'default', 'controller' => 'auth', 'action' => 'index')
		);
		$router->addRoute('login', $route);
        // Zend_Paginator::setDefaultScrollingStyle('Sliding');
        //        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
    }
	// 
	// resources.layout.layout = "layout"
	// resources.layout.layoutPath = APPLICATION_PATH "/layouts"
	// admin.resources.layout.layout = "admin"
	// admin.resources.layout.layoutPath = APPLICATION_PATH "/modules/admin/layouts"
    
    
}
