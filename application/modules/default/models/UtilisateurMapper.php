<?php
class Model_UtilisateurMapper
{
    protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data theme provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Model_DbTable_Utilisateur');
        }
        return $this->_dbTable;
    }

	public function find($id, Model_Utilisateur $utilisateur)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        return $utilisateur->setUtilisateur_id($row->utilisateur_id)
                  ->setNom($row->nom) 
		          ->setActive($row->active)
                  ->setEmail($row->email);
    }

	public function insert($data){
		$data['role_id'] = Model_Utilisateur::_ROLE_NORMAL_USER;
		$data['date_creation'] = date("Y-m-d H:i:s"); 
		$data['password'] = sha1($data['password']);
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
}