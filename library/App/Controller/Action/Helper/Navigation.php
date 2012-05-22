<?php
class App_Controller_Action_Helper_Navigation extends Zend_Controller_Action_Helper_Abstract
{
    protected $_navigation;
    protected $_helper;

    public function __construct ()
    {
        $bootstrap = $this->getFrontController()->getParam('bootstrap');
        $this->_navigation = $bootstrap->getResource('navigation');
        $this->_helper     = $bootstrap->getResource('view')
                                       ->getHelper('navigation');
    }

    public function findActive ()
    {
        $active = $this->_helper->findActive($this->_navigation);
        if (!isset($active['page'])){
            return false;
        }

        return $active['page'];
    }
	
	
    public function add (array $options)
    {
        if (false !== ($activePage = $this->findActive()) ) {
			if(isset($options['module']) and $options['module']===true){
				$options['module']=$activePage->getModule();
			}
			if(isset($options['controller']) and $options['controller']===true){
				$options['controller']=$activePage->getController();
			}
            $activePage->addPage(Zend_Navigation_Page_MVC::factory($options));
			
        }
    }
}