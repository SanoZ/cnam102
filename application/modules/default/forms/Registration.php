<?php

/**
 * This is the guestbook form.  It is in its own directory in the application 
 * structure because it represents a "composite asset" in your application.  By 
 * "composite", it is meant that the form encompasses several aspects of the 
 * application: it handles part of the display logic (view), it also handles 
 * validation and filtering (controller and model).  
 *
 * @uses       Zend_Form
 * @package    QuickStart
 * @subpackage Form
 */
class Form_Registration extends Zend_Form
{
    
        public function init()  {
        //           
        //           $this->setName('Registration');
        //           
        //           $email = new Zend_Form_Element_Text('email');
        //           $email->setLabel('Email: ')->setRequired();
        // 		  $email->setAttrib("size", 30);
        // 
        //           
        //           $name = new Zend_Form_Element_Text('name');
        //           $name->setLabel('Username: ')->setRequired();
        // 		  $name->setAttrib("size", 30);
        //           
        // 
        //           $register = new Zend_Form_Element_Submit('register');
        //           $register->setLabel('Register');
        //           $this->addElements(array($email,$name,$register));
        //           $this->setMethod('post');
        //           $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/registration/store');
        //           
        //           
        //       }
      	$firstname = new Zend_Form_Element_Text('nom');
		        $firstname->setLabel('Nom:')
		                    ->setRequired(false);

		        $lastname = new Zend_Form_Element_Text('prenom');
		        $lastname->setLabel('Prenom:')
		                    ->setRequired(false);

		        $email = new Zend_Form_Element_Text('email');
		        $email->setLabel('Email: *')
		                ->setRequired(false);
 

		        $password = new Zend_Form_Element_Password('password');
		        $password->setLabel('Password: *')
		                ->setRequired(true);

		        $confirmPassword = new Zend_Form_Element_Password('confirmPassword'); 
		        $confirmPassword->setLabel('Confirm Password: *')
		                ->setRequired(true);

		        $register =new Zend_Form_Element_Submit('register'); 
		        $register->setLabel('Sign up')
		                ->setIgnore(true);

		        $this->addElements(array(
      		        		    $firstname,
  		                        $lastname,
  		                        $email,
  		                        $password,
  		                        $confirmPassword,
  		                        $register
  		        ));
    }
        
}
