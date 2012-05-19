<?php

class Model_User 
{
    /**
     * @var table Model_DbTable_User
     */
    private $table;
    
    /**
     * @var user Zend_Db_Table_Rowset_Abstract
     */
    private $user;
    
    const _ROLE_SUPER_ADMIN = 5;
    const _ROLE_NORMAL_USER = 2;
    
 
    
    public function Model_User($user_id=null){
        $this->table = new Model_DbTable_User();
        $this->user = null;
        if($user_id!=null){
            $this->loadUser($user_id);
        }
    }

public function getCurrentUser(){
	
}
    
    
    public function createUser($email, $password, $role=2,$newFBUser = false){
        $data = array();
        $data['user_email'] = $email;
        $data['user_password'] = md5($password);
        $data['user_role'] = $role;
        $data['newRegistered'] = 1;
        $data['newFBRegistered'] = $newFBUser;
        return $this->table->insert($data);
    }
    
     
    public function isEmailRegistered($email ){
        $user = $this->table->fetchRow("user_email like '{$email}' and deleted=0 and user_id<>{$user_id}");
        if ($user){
            return true;
        }
        return false;
    }
    
    public function userExistsId($user_id){
        $user = $this->table->fetchRow("user_id = '{$user_id}'");
        if ($user){
            return true;
        }
        return false;
    }
    
    public function __get($name){
        if($this->user == null){
            return "";
        } else {
            return $this->user->$name;
        }            
    }
    
    public function __set($name, $value) {
        if($this->user != null){
            $this->user->$name = $value;
            $this->user->save();
        }
    }
    
    private function getUser($user_id)
    {
        $user = Model_DbTable_User::getUser($user_id);
        if ($user){
            $this->user = $user;
        }
    }
    
    public function hasPrivileges($module){
        if($this->user!= null){
            $table = new Model_DbTable_Module();
            $priv = $table->fetchRow("role_id = '{$user->user_role}' and module_string like '{$module}' ");
            if($priv){
                return true;
            }
        }
        return false;
    }
    
  
    public function updateUser($email,$role,$password, $user_id){
        if($role<>5){
            $data = array();
            $data['user_email'] = $email;
            $data['user_role'] = $role;
            if($password!= ''){
                $data['user_password'] = md5($password);
            }
            $this->table->update($data, "user_id = '{$user_id}'");
        }
    }
    
    
    // public function getUserByEmail($email){
    //        /**
    //         * @var user Zend_Db_Table_Row_Abstract
    //         */
    //        $user = $this->table->fetchRow("user_email like '{$email}' and deleted=0 ");
    //        if ($user){
    //            $data = $user->toArray();
    //            $data['fields'] = $this->getDescription($data['user_id']);
    //            return $data;
    //        }
    //        return false;
    //    }
    
 
    public function toArray(){
        return $this->user->toArray();
    }
    
     
    /*
     * Function that returns all active users
     * @return 
     */
    public function getAllUsers(){
        
        $table_user = new Model_DbTable_User();
        return $table_user->fetchAll("deleted = 0");        
        
    }
     
      
    
    public function isAdmin()
    {
        if (self::_ROLE_SUPER_ADMIN == $this->user_role) {
            return true;
        }
        return false;
    }
    
    
     
}
