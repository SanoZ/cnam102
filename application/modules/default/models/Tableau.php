<?php
class Model_Tableau
{	
    protected $_article_id;
    protected $_description;
    protected $_titre;
    protected $_theme_id;
    protected $_format_id;
    protected $_prix;
    protected $_date_publication;
    protected $_statut_id;
    protected $_date_modification;
    protected $_stock;
    protected $_active;
    protected $_image;
 
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
            throw new Exception('Invalid tableau property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid tableau property');
        }
        return $this->$method();
    }
 
	
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
 
           echo  $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
 	public function setArticle_id($article_id){
		$this->_article_id = (int) $article_id;
        return $this;
	}
	public function getArticle_id(){
		return $this->_article_id;
	}
    public function setDescription($text)
    {
        $this->_description = (string) $text;
        return $this;
    }
 
    public function getDescription()
    {
        return $this->_description;
    }
 
    public function setTitre($titre)
    {
        $this->_titre = (string) $titre;
        return $this;
    }
 
    public function getTitre()
    { 
        return $this->_titre;
    }
 
    public function setTheme_id($ts)
    {
        $this->_theme_id = $ts;
        return $this;
    }
 
    public function getTheme_id()
    { 
        return $this->_theme_id;
    }
 
    public function setFormat_id($id)
    { 
        $this->_format_id = (int) $id;
        return $this;
    }
 
    public function getFormat_id()
    {
        return $this->_format_id;
    }
	public function setPrix($prix)
    {
        $this->_prix = $prix;
        return $this;
    }
 
    public function getPrix()
    {
        return $this->_prix;
    }
	public function setDate_publication($date_publication)
    {
        $this->_date_publication = $date_publication;
        return $this;
    }
 
    public function getDate_publication()
    {	
		return $this->_date_publication;
    }

	public function setStatut_id($statut_id)
    {
        $this->_statut_id = (int) $statut_id;
        return $this;
    }
 
    public function getStatut_id()
    {
		return $this->_statut_id;
	}
	
	public function setDate_modification($date_modification)
    {
        $this->_date_modification = (int) $date_modification;
        return $this;
    }
 
    public function getDate_modification()
    {
		return $this->_date_modification;
	}
	
	public function setStock($stock)
    {
        $this->_stock = (int) $stock;
        return $this;
    }
 
    public function getStock()
    {
		return $this->_stock;
	}
	
	public function setActive($active)
    {
        $this->_active = (int) $active;
        return $this;
    }

    public function getActive()
    {
		return $this->_active;
	}
	public function setImage($string)
    {
        $this->_image =  $string;
        return $this;
    }

    public function getImage()
    {
		return $this->_image;
	}
	 

	function ToArray()
	{
		$array = array();
		foreach($this as $member => $data)
		{
			$array[substr($member,1,strlen($member))] = $data;
		}
		return $array;
	}
}