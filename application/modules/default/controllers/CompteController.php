<?php

require_once('ApplicationController.php');

class CompteController extends ApplicationController
{
	// public $_loggedUser;
    public function init(){
        /* Initialize action controller here */
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

            //$name = explode(' ', $fname);
            //$name = $name[0];
            //$name .= "_" . $lname[0];
            
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


            $model = new Model_User();



            //AKSIMET SPAM CHECKING!! If it fails, we mark as spam
            $spam = 0;

            try {
                //have to use a matching url and key

                $akismet = new Model_Akismet("http://" . $_SERVER["HTTP_HOST"], "8561120db9e5");

                if ($akismet->isKeyValid()) {
                    $akismet->setCommentAuthor($fname);
                    $akismet->setCommentAuthorEmail($email);
                    $akismet->setCommentAuthorURL("");
                    $akismet->setCommentContent("registration");

                    //Does AKSIMET think it's spam or not?
                    if ($akismet->isCommentSpam()) {
                        $spam = 1;
                    }
                }
            } catch (Exception $e) {
                
            }

            //END AKSIMET SPAM CHECKING!!
            //Create user
            $newUser = $model->createUser($email, $passwordclean);
            //creating the object to save the fields
            $user_fields_model = new Model_UserField();
            $fields = array();
            $fields[1] = $fname;
            $fields[2] = $lname;
            $fields[3] = $dob;
            $fields[15] = $gender;
            $fields[16] = $country;
            
            
            //saving the new fields in teh database
            $user_fields_model->saveUserFieldValues($newUser, $fields);
            
                
            //if it's spam.. don't log them in, just redirect them
            if ($spam == 1) {

                return $this->_helper->redirector('awaiting');
            }



            //we just registered (setting session for wall)
            $users_session->justRegistered = true;

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

 }

