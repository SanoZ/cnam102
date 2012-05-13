<?php

require_once('ApplicationController.php');

class LoginController extends ApplicationController
{
    protected $_checkActionCookies = false;
    
    public function init()
    {
        //cahnging the layout
        $layout = Zend_Layout::getMvcInstance();
        // Set a layout script path:
        $layout->setLayoutPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'default');
        // choose a different layout script:
        $layout->setLayout('layout2');
        $this->loadVariables();
        
    }
    /*
     * This function is used to login the user using ajax
     */

    
    private function loadVariables()
    {
        $user = new Model_User();
        $this->view->countries = $user->getAllUserCountries();
    }
    
    public function indexAction() {
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/stores");
            
        }

        $this->view->seo_title = "Registration";
        $this->view->meta_desc = "login";
        
        $this->view->primary = 'login';

        $result = array('result' => false);
        //User Already registered
    }

    public function loginAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/stores");
            
        }
        
        //here the authentication occurs
        if ($this->getRequest()->isPost()) {
            $auth = new Model_Auth();
            $authAdapter = $auth->getAuthAdapter();
            $email = $this->_request->getParam('loginemail',"");
            $password = $this->_request->getParam('loginpassword',"");
            
            if($email != "" && $password !=""){
                if ($auth->validateUser($email, $password)) {
                    $this->_redirect("/stores");
                } else {
                    $this->_redirect("/login/invalidemail");
                }
            } else {
                $this->_redirect("/login/invalidemail");
            }
        } else {
            $this->_redirect("/login");
        }
        
    }

    
    
    /*
     * Function handles logout 
     */

    public function logoutAction()
    {
        $_usersSession = new Zend_Session_Namespace('users_session');
        $_usersSession->disableAutoLogin = true;
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector->gotoUrl("/");
        die;
    }
    
	
	
    public function displayLoginAction() {
        if($this->_request->getParam("ajax",false)){
            $this->_helper->layout->disableLayout();
        } else {
             $this->_forward("index");
        }
    }
	
    public function displayForgotPasswordAction() {
        
    }
	

    //Error Messages
    public function invalidemailAction() {
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/stores");
            
        }
        
        $this->view->seo_title = "Invalid Email";
        $this->view->meta_desc  = "Invalid Email";
        
        
        $this->view->primary = 'login';

        $form = new Form_Registration();
        $this->view->form = $form;
        $this->view->active_register = true;

        $this->view->errorMessage = "Password and email doesn't match, sign up to Ollio now!";
                
        $this->render('index');
    }

    //Error Messages
    public function emailinuseAction() {
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/stores");
            
        }
        
        $this->view->seo_title = "Email already in use";
        $this->view->meta_desc  = "Email already in use";
        
        $this->view->primary = 'login';
        $form = new Form_Registration();
        $this->view->form = $form;
        $this->view->active_register = true;

        $this->view->errorMessageRegister = "The email you have entered is already in use. Please try again.";
        $this->render('index');
    }

    //Error Messages
    public function credentialserrorAction() {
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/stores");
            
        }
        
        $this->view->seo_title = "Credentials Error";
        $this->view->meta_desc  = "Credentials Error";
        $this->view->primary = 'login';
        
        $form = new Form_Registration();
        $this->view->form = $form;
        $this->view->active_register = true;

        $this->view->errorMessageLogin = "Your email & password don't match.<br>Please try again.";
        $this->render('index');
    }
    

    public function usernametakenAction() {
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/stores");
            
        }
        
        $this->view->seo_title = "Username already in use";
        $this->view->meta_desc  = "Username already in use";
        $this->view->primary = 'login';
        $form = new Form_Registration();
        $this->view->form = $form;
        $this->view->active_register = true;

        $this->view->errorMessageRegister = "The username you entered is already taken. Please enter an alternative.";
        $this->render('index');
    }

    public function awaitingAction() {
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect("/stores");
            
        }
        
        $this->view->seo_title = "Awaiting";
        $this->view->meta_desc  = "Awaiting";
        $this->view->errorMessageRegister =
                "Thank you for registering. Your account is awaiting approval by an admin and you will receive an email shortly.";
        $this->render('index');
    }

    public function facebookAction(){
        
        $model_face = new Model_Facebook();
        if($model_face->register())
            $this->_redirect('/stores');
        else
            $this->_redirect('/login');
        
    }

    

}

?>

