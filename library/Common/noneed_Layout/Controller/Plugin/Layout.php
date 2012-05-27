<?php

class Common_Layout_Controller_Plugin_Layout extends Zend_Layout_Controller_Plugin_Layout
{
   
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    { 
        switch ($request->module)
        {
            case 'admin':
echo "layout admin";
                $view = Zend_Layout::getMvcInstance()->getView();
                $this->_moduleChange('admin');
                break;
            default:
echo "layout default";
                $view = Zend_Layout::getMvcInstance()->getView();
                $this->_moduleChange('default');
                break; 
        }
    }

    protected function _moduleChange($moduleName)
    { 
        $this->getLayout()->setLayoutPath( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . $moduleName); 
        $this->getLayout()->setLayout('layout');
    }
   
}