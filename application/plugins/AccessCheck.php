<?php

class Plugin_AccessCheck extends Zend_Controller_Plugin_Abstract {

    private $_acl = null;
    private $_auth = null;

    public function __construct(Zend_Acl $acl, Zend_Auth $auth) {
        $this->_acl = $acl;
        $this->_auth = $auth;
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        $resource = $request->module;
        $identity = $this->_auth->getStorage()->read();
        $role = 'guest';
        
        if($resource == 'default'){
            return;
        }
var_dump($identity);die;        
        if($identity){
            $role = $identity->role_id;
            //has privileges
            if($role != 2){
                if (!$this->_acl->isAllowed($role, $resource)) {
                    //if i am not allowed to acces the admin area take the user to the login page of default area
                    $request->setModuleName('default');
                    $request->setControllerName('index');
                    $request->setActionName('index');
                } else {
                    //if the user is allowed to the backend, check the module
                    if($request->controller != 'index' || $request->controller != 'login'){
                        $user = new Model_User($identity->user_id);
                        //checks if trhe user has access to the correct module
                        if(!$user->hasPrivileges($request->controller)){
                            //if the user does not have access to the module, then it is redirected to the index page of the admin section
                            
                            $request->setModuleName('admin');
                            $request->setControllerName('index');
                            $request->setActionName('index');
                            
                        }
                    }
                    
                    
                }
            }
        } else {
            if (!$this->_acl->isAllowed($role, $resource)) {
                $request->setModuleName('admin');
                $request->setControllerName('login');
                $request->setActionName('index');
            } 
        }
    }

}

?>