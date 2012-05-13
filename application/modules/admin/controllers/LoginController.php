<?php

class Admin_LoginController extends Zend_Controller_Action {

    
    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        //disables the layout so the user wont look at the menu
        $this->_helper->layout->disableLayout();
        ///Redirected if logged in
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_redirect('/admin/index');
        } else {
            $request = $this->getRequest();
            //get zend form
            $form = new Admin_Form_Login();
            //here the authentication occurs
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->_request->getPost())) {
                    $email = $form->getValue('user_name');
                    $password = $form->getValue('user_password');
                    $authModel = new Model_Auth();
                    if($authModel->validateUser($email, $password)){
                        $this->_redirect('/admin/index');
                    } else{
                        $this->view->message = $authModel->getErrorMessage();
                    }
                } else {
                    $this->view->message = "Please enter credentials";
                }
            }
        }
    }

    

    /*
     * Function handles logout 
     */
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        die('logout');
        $this->_redirect('admin/login');
    }

}

