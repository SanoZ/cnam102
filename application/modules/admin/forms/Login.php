<?php

class Admin_Form_Login extends Zend_Form {

    public function init() {
        $user_name = new Zend_Form_Element_Text('user_name');
        $user_name->setLabel('Login:');

        $user_password = new Zend_Form_Element_Password('user_password');
        $user_password->setLabel('Password:');

        $add = new Zend_Form_Element_Submit('add');
        $add->setLabel('Login');
        $this->addElements(array($user_name, $user_password, $add));
        $this->setMethod('post');
    }

}

