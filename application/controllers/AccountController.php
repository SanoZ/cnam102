<?php

require_once('ApplicationController.php');

class AccountController extends ApplicationController
{

    public function init() {
        //menu location
        $this->view->store_section = true;
    }

    public function indexAction() {
        $this->view->seo_title = "My Account";
        $this->view->meta_desc = "Account area";

        $loggedin = $this->_loggedUser;
        if ($loggedin) {

            $data = $this->_loggedUser->getDescription();
            $this->view->name = $data['Name']['value'];
            $this->view->email = $this->_loggedUser->user_email;
            $this->view->user_id = $this->_loggedUser->user_id;

            //break date of birth into chunks and assign to variables
            if($data['DOB']['value']) {
                $dobbits = explode("/", $data['DOB']['value']);
                $this->view->dob_d = $dobbits[0];
                $this->view->dob_m = $dobbits[1];
                $this->view->dob_y = $dobbits[2];
            } else {
                $this->view->dob_d = '';
                $this->view->dob_m = '';
                $this->view->dob_y = '';
            }

            $this->view->user_data = $data;
            
            $this->view->update = $this->_request->getParam('update', 0);
            
            // Get list of stores
            $this->view->stores = $this->_loggedUser->getOwnedStores($this->_loggedUser->user_id);
            
            //Get list of countries
            $this->view->countries = $this->_loggedUser->getAllUserCountries();
            
            $this->view->is_user_FB = $this->_loggedUser->isFBUser();
        } else {
            $this->_redirect("/login");
        }        
    }

    public function registerAction() {

        $request = $this->getRequest();
        $form = new Form_Registration();

        $users_session = new Zend_Session_Namespace('users_session');

        if ($this->getRequest()->isPost()) {

            $email = $this->_request->getParam('email');
            $passwordclean = $this->_request->getParam('password');
            $fname = $this->_request->getParam('fname');
            $lname = $this->_request->getParam('lname');
            $dob_d = $this->_request->getParam('dob_d');
            $dob_m = $this->_request->getParam('dob_m');
            $dob_y = $this->_request->getParam('dob_y');
            $gender = $this->_request->getParam('gender');
            $country = $this->_request->getParam('country');
            
 
            $dob = $dob_d . "/" . $dob_m . "/" . $dob_y;
            $fname = trim($fname);
            $lname = trim($lname);
            $country = trim($country);
            
            //make sure email is valid

            $validator = new Zend_Validate_EmailAddress();

            if (!$validator->isValid($email)) {
                // email is invalid, redirect and don't continue
                return $this->_redirect('/login/invalidemail');
                die;
            }

            $user_model = new Model_User();
            $user = $user_model->getUserByEmail($email);
            if ($user != null) {
                // email is invalid, redirect and don't continue
                $this->_redirect('/login/emailinuse');
                die;
            }
  
            //AKSIMET SPAM CHECKING!! If it fails, we mark as spam
            $spam = 0;

 
            }

            //END AKSIMET SPAM CHECKING!!
            //Create user
            $newUser = $model->createUser($email, $passwordclean);
            //creating the object to save the fields
            // $user_fields_model = new Model_UserField();
            // $fields = array();
            // $fields[1] = $fname;
            // $fields[2] = $lname;
            // $fields[3] = $dob;
            // $fields[15] = $gender;
            // $fields[16] = $country;
            
            
            //saving the new fields in teh database
            // $user_fields_model->saveUserFieldValues($newUser, $fields);
    
 
            //Mail rego link to user
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

            $email_send = new Model_Email();
            $email_send->sendEmailLoginRegisterUser($newUser, $passwordclean, $email);

            //login the user 
            $auth = new Model_Auth();
            //should always be valid becuase it was just created
            $auth->validateUser($email, $passwordclean);

            //TODO redirect to page user was on
            $this->_helper->redirector->gotoUrl('/account/index/new/1');
        }
        $this->_helper->redirector->gotoUrl('/login');
    }
 

    public function saveAction() {
        $model_user = new Model_User();
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();

            $model_user->getCurrentUser();
            //reseting password if both passwords are the same
            $pass1 = $request->getParam('user_pass', "");
            $pass2 = $request->getParam('user_pass2', "");
            if(sizeof($pass1) > 0 && $pass1 != ''){
                if($pass1 != $pass2){
                    $this->_redirect("/account/index/update/2");
                    return;
                }
            }
            
            if ($pass1 == $pass2 && sizeof($pass1) > 0 && $pass1 != '') {
                //reseting the password

                $model_user->user_password = md5($pass2);
            }
            //getting the fileds of the user
            $fname = $request->getParam("name", "");
            $lname = $request->getParam("lname", "");
            $bio = $request->getParam("bio", "");



            $gender = $this->_request->getParam('gender');
            $country = $this->_request->getParam('country');
            
            $dob_d = $this->_request->getParam('dob_d');
            $dob_m = $this->_request->getParam('dob_m');
            $dob_y = $this->_request->getParam('dob_y');
            $dob = $dob_d . "/" . $dob_m . "/" . $dob_y;

            //creating the array with the infromation of the fields of the user
         
            //saving new user field values
            $user_data->saveUserFieldValues($model_user->user_id, $fields);


            //Saving the image of the user
            $image = $_FILES['profile_image'];
            $this->_redirect("/account/index/update/1");
        }
        $this->_redirect("/account");
    }
    
    public function forgotPasswordAction(){
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $user_model = new Model_User();
        $global_model = new Model_Globals();

        $result = array();
        $email = $this->_request->getParam('email_forgot',"");
        
        $result['text'] = "<p>The email entered is invalid</p>";
        if($email != ''){
            $user = $user_model->getUserByEmail($email);
            if($user != null){
                //generating the new password
                $user_model->loadUser($user['user_id']);
                $pass = $global_model->genRandomString(10);
                $user_model->user_password =  md5($pass);

                //$this->sendEmailForgotPasword($email, $pass);
                $model_email = new Model_Email();
                $model_email->sendEmailForgotPasword($email,$pass);

                $result['text'] = "<p class='register-success'>Your password has been sent out.<br/>Please check your email.</p>";
            } else {
                $result['text'] = "<p>There in no user with this email account.</p>";
            }
        } 
        $this->_helper->json->sendJson($result);
    }
    
     
    private function stringnl2br($input) {
        $out = str_replace( "\r\n", "<br/>", $input );
        return $out;
        
    }
     
    
    
}


