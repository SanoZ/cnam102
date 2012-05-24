<?php 
class Application_Model_Tableaux extends Zend_Db_Table_Abstract
{
	protected $_comment;
	    protected $_created;
	    protected $_email;
	    protected $_id;

	    public function __set($name, $value);
	    public function __get($name);

	    public function setComment($text);
	    public function getComment();

	    public function setEmail($email);
	    public function getEmail();

	    public function setCreated($ts);
	    public function getCreated();

	    public function setId($id);
	    public function getId();