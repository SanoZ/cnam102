<?php

class Plugin_LayoutSelector extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $request->getModuleName();

		// check the module and automatically set the layout
		$layout = Zend_Layout::getMvcInstance();
 
		switch ($module) {
			case 'backend': 
                $layout->setLayout('backend');
                break;

            case 'frontend': 
                $layout->setLayout('default');
			    break;
		}
	}
}