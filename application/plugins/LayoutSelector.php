<?php

class Plugin_LayoutSelector extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $request->getModuleName();
		/*$controller = $request->getControllerName();

        $front_controller = Zend_Controller_Front::getInstance();
        $error_handler = $front_controller->getPlugin('Zend_Controller_Plugin_ErrorHandler');
		$error_handler->setErrorHandlerModule($module);*/

		// check the module and automatically set the layout
		$layout = Zend_Layout::getMvcInstance();

		switch ($module) {
			case 'backend':
                $layout->setLayout('backend');
                break;

            case 'frontend':
                $layout->setLayout('frontend');
			    break;
		}
	}
}