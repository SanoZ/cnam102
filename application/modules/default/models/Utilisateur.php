<?php

//class Model_Utilisateur_Exception extends Exception {}
	
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

 
	public function getCurrentUser(){
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getStorage()->read();
        if($identity){
            return $identity;
        } else {
            return false;
        }
    }

    public function insert($data){
		$data['role_id'] = self::_ROLE_NORMAL_USER;
		$data['date_creation'] = date("Y-m-d H:i:s"); 
		$data['password'] = sha1($data['password']);
		// $data['salt'] = $data['password'];
		$user = new Model_DbTable_Utilisateur();
	    $user->insert($data);
	}
    
	public function update($data){
		if($data["confirmPassword"] == $data['password']){
			$user_lambda = Model_DbTable_Utilisateur::getUserByEmail($data['email']);
			if(empty($user_lambda) ){ 
				$this->_save($data);
			}else{
				if($user_lambda->utilisateur_id == $data['utilisateur_id']){
					$this->_save($data);
				}else{
					throw new Model_DbTable_Utilisateur_Exception("Désolé, l'email que vous avez renseigné existe déjà dans notre base.");
				}
			}
		}else{
			throw new Model_DbTable_Utilisateur_Exception("Désolé, le password et sa confirmation ne sont pas identique. Veuillez re-essayer.");
		}
	}
   
	protected function _save($data){
		$user_model = Model_DbTable_Utilisateur::getUserById($data["utilisateur_id"]);
		if($user_model){
			$data['password'] = sha1($data['password']);
			$_db = Zend_Db_Table::getDefaultAdapter();
			$_db->update($data, array("utilisateur_id" =>$data["utilisateur_id"]) );
		}else{
			throw new Model_DbTable_Utilisateur_Exception("Site en maintenance.");
		}
	}
  
    public function isAdmin()
    {
        if (self::_ROLE_SUPER_ADMIN == $this->role_id) {
            return true;
        }
        return false;
    }
    
    
     
}
