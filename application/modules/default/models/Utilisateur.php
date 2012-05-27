<?php

//class Model_Utilisateur_Exception extends Exception {}
	
class Model_Utilisateur  
{    protected $_utilisateur_id;
    protected $_nom;
    protected $_prenom;
    protected $_addresse1;
    protected $_addresse2;
    protected $_email;
    protected $_ville;
    protected $_active;
    protected $_date_creation;
    protected $_role_id; 
 
	
    const _ROLE_SUPER_ADMIN = 2;
    const _ROLE_NORMAL_USER = 1;

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid utilisateur property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid utilisateur property');
        }
        return $this->$method();
    }
 
	public function setUtilisateur_id($user_id){
		$this->_utilisateur_id = (int) $user_id;
        return $this;
	}
	public function getUtilisateur_id(){
		return $this->_utilisateur_id;
	}
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
 
    public function setNom($text)
    {
        $this->_nom = (string) $text;
        return $this;
    }

	public function getNom(){
		return $this->_nom;
	}
	public function setPrenom($text)
    {
        $this->_nom = (string) $text;
        return $this;
    }

	public function getPrenom(){
		return $this->_nom;
	}
	public function setAdresse1($text)
    {
        $this->_adresse1 = (string) $text;
        return $this;
    }

	public function getAdresse1(){
		return $this->_adresse1;
	}
	public function setAdresse2($text)
    {
        $this->_adresse2 = (string) $text;
        return $this;
    }

	public function getAdresse2(){
		return $this->_adresse2;
	}
	public function setEmail($text)
    {
        $this->_email = (string) $text;
        return $this;
    }

	public function getEmail(){
		return $this->_email;
	}
	public function setVille($text)
    {
        $this->_ville = (string) $text;
        return $this;
    }

	public function getVille(){
		return $this->_ville;
	}
	public function setActive($text)
    {
        $this->_active = $text;
        return $this;
    }

	public function getActive(){
		return $this->_active;
	}
	public function setDate_creation($text)
    {
        $this->_date_creation = $text;
        return $this;
    }

	public function getDate_creation(){
		return $this->_date_creation;
	}	
	public function setRole_id($text)
    {
        $this->_role_id = $text;
        return $this;
    }

	public function getRole_id(){
		return $this->_role_id;
	}
    /**
     * @var table Model_DbTable_User
     */
    private $table;
     
    public function isAdmin()
    {
        if (self::_ROLE_SUPER_ADMIN == $this->_role_id) {
            return true;
        }
        return false;
    }
    
    
     
}
