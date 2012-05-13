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
    
    public function isFollowingProduct($productId)
    {
        $table = new Model_DbTable_User();
        return (bool) $table->getAdapter()->query('SELECT `user_id` FROM `wish_list` WHERE `user_id` = ? AND `product_id` = ? ', array(
            $this->user_id, 
            $productId
        ))->fetch();
    }
    
    public function Model_User($user_id=null){
        $this->table = new Model_DbTable_User();
        $this->user = null;
        if($user_id!=null){
            $this->loadUser($user_id);
        }
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
    
    
    
    public function loadUser($user_id){
        $this->getUser($user_id);
    }
    
    public function isEmailRegistered($email, $user_id=0){
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
//        $user = $this->table->fetchRow("user_id = '{$user_id}'");
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
    /**
     * Function that retreives al;l the users that have access to the cms
     * @return 
     */
    public function getUsers($search=""){
        
        //1 is guest and 2 is registered
        $select = $this->table->select();
        $select->where(" deleted = 0 and user_email like'%{$search}%'");
        return $select;
        
        
    }
    
    /**
     *  This fiunction returns the fields of a user with the following structure:
     *  key = field name
     *  value = array (value, type, id)
     * @param type $user_id
     * @return type array
     */
    public function getDescription($user_id=null){
        if($user_id != null){
            $user_id_search = $user_id;
        } else {
            if($this->user != null)
                $user_id_search = $this->user->user_id;
            else
                return array();
        }
        if($user_id_search != null){
            $db = $this->table->getAdapter();
            $sql = ("SELECT f.user_field_id id, f.user_field_name `name`, f.user_field_type `type`, fv.user_field_value `value` , u.user_id
            FROM `user_field` f left join user_field_value fv on f.user_field_id  = fv.user_field_id and fv.user_id = {$user_id_search}
            left join user u on u.user_id = fv.user_id and u.deleted=0 and fv.user_id = {$user_id_search}");
            $fields = $db->fetchAll($sql);
            
            $fieldsArray = array();
            foreach($fields as $field){
                $fieldsArray[$field['name']] = array('value' => $field['value'], 'type'=>$field['type'], 'id'=>$field['id']);
            }
            return $fieldsArray;
        } else {
            return array();
        }
    }
    
    public function getCurrentUser(){
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getStorage()->read();
        if($identity){
            $this->getUser($identity->user_id);
            return true;
        } else {
            return false;
        }
    }
    
    public function getCurrentUserDetails(){
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getStorage()->read();
        if($identity){
            $this->getUser($identity->user_id);
            return $this->user;
        } else {
            return false;
        }
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
    
    /**
     * Function that retreives all the users that are following an specific user 
     * @return 
     */
    public function searchUserFollowers($user_id, $search=""){
        
        $db = $this->table->getAdapter();
        $sql = "SELECT f.follower_user_id as user_id, ufv1.user_field_value as name, ufv2.user_field_value as last_name
                FROM `follow_user` as f
                JOIN `user` as u on f.follower_user_id = u.user_id and u.deleted = 0
                JOIN `user_field_value` as ufv1 on f.follower_user_id = ufv1.user_id and ufv1.user_field_id = 1
                JOIN `user_field_value` as ufv2 on f.follower_user_id = ufv2.user_id and ufv2.user_field_id = 2
                where f.followed_user_id = '{$user_id}'
                and (ufv1.user_field_value like '%{$search}%' or ufv2.user_field_value like '%{$search}%')";
        $users = $db->fetchAll($sql);
        
        if($users){
            return $users;
        }
        return array();
    }
    
    public function getUserByEmail($email){
        /**
         * @var user Zend_Db_Table_Row_Abstract
         */
        $user = $this->table->fetchRow("user_email like '{$email}' and deleted=0 ");
        if ($user){
            $data = $user->toArray();
            $data['fields'] = $this->getDescription($data['user_id']);
            return $data;
        }
        return false;
    }
    
    static public function getUserByFacebookId($facebookId)
    {
        $_table = new Model_DbTable_User();
        $user = $_table->getDefaultAdapter()->query('SELECT * FROM `user` WHERE `fb_user_id` = ? AND deleted = 0', $facebookId)->fetchObject();
        if (!empty($user)) {
            return $user;
        }
        return false;
    }
        
    public function getUserById($user_id){
        /**
         * @var user Zend_Db_Table_Row_Abstract
         */
        $user = $this->table->fetchRow("user_id = '{$user_id}' and deleted=0 ");
        if ($user){
            $data = $user->toArray();
            $data['fields'] = $this->getDescription($data['user_id']);
            return $data;
        }
        return false;
    }
    
    public function toArray(){
        return $this->user->toArray();
    }
    
    
    public function removeProfileImage($user_id){
        $model_image = new Model_ElementImage();
        $model_image->deleteImage($user_id, Model_ElementImage::$IMAGE_TYPE_PROFILE, getcwd() . "/images/profile-default.png");
        
    }
    
    // Get owned stores
    public function getOwnedStores($user_id){
        $table_store = new Model_DbTable_Store();
        $db = $table_store->getAdapter();
        
        $sql = "select s.*, ap.active as active_affiliate
                from store_owner so
                join store s on so.store_id = s.store_id
                left join affiliate_program ap on so.store_id = ap.store_id
                where so.user_id = '{$user_id}'
                order by store_name asc";
        
        return $db->fetchAll($sql);
    }
        
    /*
     * Function that returns all active users
     * @return 
     */
    public function getAllUsers(){
        
        $table_user = new Model_DbTable_User();
        return $table_user->fetchAll("deleted = 0");        
        
    }
    
    public function updateUserFbInfo($user_email, $fb_id, $fb_username, $token){
        $table_user = new Model_DbTable_User();
        return $table_user->update(array("fb_user_id" => $fb_id, "fb_user_name" => $fb_username, 'fb_token' => $token), "user_email = '{$user_email}'");
    }
    
    public function getFacebookId($user_id){
        $table_user = new Model_DbTable_User();
        $result = $table_user->fetchRow("user_id = '$user_id'");
        if ($result && isset($result['fb_user_id']))
            return $result['fb_user_id'];
        else
            return false;
    }
    
    // public function getAllUserCountries()
    //     {
    //         $table_country = new Model_DbTable_Country();
    //         
    //         
    //        $fields = $table_country->fetchAll();
    //         
    //        $fieldsArray = array();
    //        $fieldsArray[0] =  'Please select';
    //        foreach($fields as $field){
    //             $fieldsArray[($field['name'])] = ($field['name']);
    //        }
    //        return $fieldsArray;
    //     }
    public function getUserProductsCount($user_id){
        $table_wishList = new Model_DbTable_WishList();
        $db = $table_wishList->getAdapter();
        
        $sql = "select count(*) product_count
                from wish_list
                where user_id = {$user_id}";
                
        $count = $db->fetchRow($sql);
        return $count['product_count'];
    }
    
    public function isAdmin()
    {
        if (self::_ROLE_SUPER_ADMIN == $this->user_role) {
            return true;
        }
        return false;
    }
    
    public function isFBUser()
    {
        if ( $this->fb_user_id !== "") { //not empty it means its a FB user
            return true;
        }
        return false;
    }
    
    public function deleteComment($commentId, $type)
    {
        $_table = new Model_DbTable_User();
        $_db = $_table->getAdapter();
        if (true === $this->isAdmin()) {
            $_db->query('DELETE FROM `discussion` WHERE `discussion_id` = ? AND `discussion_type` = ?', array($commentId, $type));
            return true;
        } else {
            $_db->query('DELETE FROM `discussion` WHERE `discussion_id` = ? AND `discussion_type` = ? AND `user_id` = ?', array($commentId, $type, $this->user_id));
            return true;
        }
        
        return false;
    }
    
    public function getDetails()
    {
        return Model_DbTable_User::getUserDetails($this->user_id);
    }
}
