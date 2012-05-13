<?php

class Model_Acl extends Zend_Acl {
  public function __construct() {
    
    $this->add(new Zend_Acl_Resource('admin'));
    $this->add(new Zend_Acl_Resource('default'));


    $this->addRole(new Zend_Acl_Role('1'));//Guest Access
    $this->addRole(new Zend_Acl_Role('2'), '1');//registered user 
    $this->addRole(new Zend_Acl_Role('3'), '2');//access backend Admin  
    $this->addRole(new Zend_Acl_Role('4'), '2');//access backend Personalized
    $this->addRole(new Zend_Acl_Role('5'), '2');//super admin
    $this->addRole(new Zend_Acl_Role('guest'), '1');

    //Guests users
    $this->allow('1', 'default');    
    //deny geust users to access admin area
    $this->deny('1', 'admin');
    //Admin and personalized user
    $this->allow('3', 'admin');


  }
}
