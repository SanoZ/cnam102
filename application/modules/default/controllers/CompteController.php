<?php

require_once('ApplicationController.php');

class CompteController extends ApplicationController
{
	// public $_loggedUser;
    public function init(){
        /* Initialize action controller here */
		if($this->_loggedUser){
			$this->_helper->redirector('signup', 'auth');
		}
    }

	public function indexAction(){
        $form = new Form_Registration();
        $this->view->form = $form;
		$request = $this->getRequest();
		if ($request->isPost()) { 
			if ($form->isValid($request->getPost())) {
				$user = new Model_Utilisateur();
				try{
					$data = $form->getValues();
					$data["utilisateur_id"] = $this->_loggedUser->utilisateur_id ;
					if ( $user->update($data) ) {
						$this->view->message = "Vos données ont été mis à jour.";
					}else{
						$this->view->message = "Erreur, Vérifiez votre email et/ou mot de passe;";
					}
				}catch(Exception $e){
					$this->view->errorMessage = $e->getMessage();
				}
			}
		}
		$this->view->form = $form;
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
 

 

 }

