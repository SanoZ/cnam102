<?php

class Admin_UserController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $model_user = new Model_User();
        
        // getting search parameter
        $search = $this->getRequest()->getParam("search", "");
        $this->view->search = $search;
        
        
        $users = $model_user->getUsers($search);
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($users));
        $paginator->setItemCountPerPage(50)
                ->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;

        $modelFields = new Model_UserField();

        $this->view->userFields = $modelFields->getUserFiledArray(4);
    }

    public function formAction() {
        //create new form
        $request = $this->getRequest();
        
        //Event model declaration
        $user_model = new Model_User();
        
        //getting the roles
        $roles_module = new Model_Role();
        $this->view->roles = $roles_module->getActiveRoles();

        if ($this->getRequest()->isPost()) {
            //checks that the user does not 
            //checks to see if this is an edit or not               
            if ($this->getRequest()->getPost('user_id')) {
                $user_id = $this->getRequest()->getParam('user_id', 0);
                if (!$user_model->isEmailRegistered($this->_request->getParam('email', ''), $user_id)) {
                    $email = $this->_request->getParam('email', '');
                    $password = $this->_request->getParam('password', '');
                    $role = $this->_request->getParam('cms_access_role', null);
                    //updating user info
                    $user_model->updateUser($email,$role,$password, $user_id);
                    //now saving the information of the fields
                    $fields = $this->_request->getParam('field', array());
                    //creating the module for the management of fields
                    $user_fields_model = new Model_UserField();
                    //saving the new fields in teh database
                    $user_fields_model->saveUserFieldValues($user_id, $fields);
                }
                echo "User Updated";
            } else {
                if (!$user_model->isEmailRegistered($this->_request->getParam('email', ''))) {
                    //getting the parameters from the post
                    $email = $this->_request->getParam('email', '');
                    $password = $this->_request->getParam('password', '');
                    $role = $this->_request->getParam('cms_access_role', null);
                    //saving the user
                    $user_id = $user_model->createUser($email, $password, $role);
                    //getting the fields that the user inserted
                    $fields = $this->_request->getParam('field', array());
                    //creating the object to save the fields
                    $user_fields_model = new Model_UserField();
                    //saving the new fields in teh database
                    $user_fields_model->saveUserFieldValues($user_id, $fields);
                    //saving the access of the modules
                   
                    echo "User Added";
                } else {
                    echo "User already registered with that email";
                }
            }

            //redirect back to events table
            //determines if it is an edit
            //setting new title and content
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
        } else if ($this->getRequest()->getParam('edituser')) {
            //get Models details

            $user = new Model_User($this->getRequest()->getParam('edituser'));
            $this->view->user = $user;
            $this->view->user_details = $user->getDescription();
            $this->view->isedit = true;
        } else {
            
        }
    }

    public function deleteAction() {
        $model = new Model_User();
        $box = $_POST['delete_box'];
        if ($_POST['delete_box']) {
            while (list ($key, $val) = @each($box)) {
                $model->DeleteUser($val);
            }
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            echo "User Deleted.";
        }
    }

    public function displayFieldsAction() {
        $user_id = $this->_request->getParam("user_id", 0);
        $model = new Model_User();
        $this->view->userFields = $model->getDescription($user_id);
    }

}

