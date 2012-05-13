<?php

class Model_Auth {

    private $error;

    public function __construct() {
        $this->error = "";
    }

    public function validateUser($email, $password) {
        
        $authAdapter = $this->getAuthAdapter();
        if ($email != '' && $password != '') {
            $password = md5($password);
            $authAdapter->setIdentity($email);
            $authAdapter->setCredential($password);

            $auth = Zend_Auth::getInstance();
            $authNamespace = new Zend_Session_Namespace('Zend_Auth');
            $authNamespace->setExpirationSeconds(2700);
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $identity = $authAdapter->getResultRowObject();
                
                $authStorage = $auth->getStorage();
                $authStorage->write($identity);
                return true;
            } else {
                $this->error = "Your credentials did not match, please try again";
                return false;
            }
        } else {
            $this->error = "Your credentials did not match, please try again";
            return false;
        }
    }

    public function loginFacebookUser($email) {
        
        $authAdapter = $this->getAuthAdapter();
        if ($email != '') {
            $authAdapter->setIdentity($email);
            
            $user_model = new Model_User($email);
            $ziller_user = $user_model->getUserByEmail($email);
            
            $authAdapter->setCredential($ziller_user["user_password"]);

            $auth = Zend_Auth::getInstance();
            $authNamespace = new Zend_Session_Namespace('Zend_Auth');
            $authNamespace->setExpirationSeconds(2700);
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $identity = $authAdapter->getResultRowObject();
                
                $authStorage = $auth->getStorage();
                $authStorage->write($identity);
                return true;
            } else {
                $this->error = "Your credentials did not match, please try again";
                return false;
            }
        } else {
            $this->error = "Your credentials did not match, please try again";
            return false;
        }
    }

    /**
     * Method the returns the authentication method used
     * @return Zend_Auth_Adapter_DbTable
     */
    public function getAuthAdapter() {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('user');
        $authAdapter->setCredentialColumn('user_password');
        $authAdapter->setIdentityColumn('user_email');
        $authAdapter->setCredentialTreatment('? and deleted = 0');
        $authAdapter->setAmbiguityIdentity(true);
        return $authAdapter;
    }
    
    public function getErrorMessage(){
        return $this->error;
    }
    
    public function checkModulePermisions($user_email, $module){
        
    }

}


