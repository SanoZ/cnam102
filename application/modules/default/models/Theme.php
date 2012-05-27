<?php
class Model_Theme
{	
    protected $_theme;
    protected $_theme_id;
    protected $_date_creation; 
    protected $_active;
 
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
            throw new Exception('Invalid theme property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid theme property');
        }
        return $this->$method();
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
 
	public function setTheme_id($theme_id){
		$this->_theme_id = (int) $theme_id;
        return $this;
	}
	public function getTheme_id(){
		return $this->_theme_id;
	}
    public function setTheme($text)
    {
        $this->_theme = (string) $text;
        return $this;
    }
 
    public function getTheme()
    {
        return $this->_theme;
    }
   
	public function setDateCreation($date_creation)
    {
        $this->_date_creation = $date_creation;
        return $this;
    }
 
    public function getDateCreation()
    {	
		return $this->_date_creation;
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
}