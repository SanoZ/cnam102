<?php

class Model_Utilisateur  
{
	protected $_tableClass         = 'Model_DbTable_Utilisateur';
    protected $_data               = array(
        'utilisateur_id'			=> '',
        'nom'                         => '',
        'prenom'                          => '', 
        'adresse1'                          => '', 
        'adresse2'                          => '', 
        'cp'                          => '', 
        'email'                          => '',
        'ville'                          => '', 
        'active'                          => '', 
        'date_creation'                          => '', 
       // 'password'                          => '',
        //'salt'                          => '',
        'role_id'                          => '' //1 user 2 admin
    );
    /**
     * @var table Model_DbTable_User
     */
    private $table;
    
    /**
     * @var user Zend_Db_Table_Rowset_Abstract
     */
    private $user;
    
    const _ROLE_SUPER_ADMIN = 2;
    const _ROLE_NORMAL_USER = 1;

 
    public function insert($data){
		$data['role_id'] = self::_ROLE_NORMAL_USER;
		$data['date_creation'] = date("Y-m-d H:i:s"); 
		$data['password'] = sha1($data['password']);
		// $data['salt'] = $data['password'];
		$user = new Model_DbTable_Utilisateur();
	    $user->insert($data);
	}
      //   
      // public function createUser($data, $role=self::_ROLE_NORMAL_USER ){
      //     $data = array();
      //     $data['user_email'] = $email;
      //     $data['user_password'] = sha1($password);
      //     $data['user_role'] = $role;
      //     return $this->table->insert($data);
      // }
      // 
     
    public function isEmailRegistered($email ){
		$this->table = new Model_DbTable_Utilisateur();
        $user = $this->table->fetchRow(" email like '{$email}'   ");
        if ($user){
            return true;
        }
        return false;
    }
    
    public function userExistsId($user_id){
		$this->table = new Model_DbTable_Utilisateur();
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
