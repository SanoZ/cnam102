<?php
class form_Contact extends Zend_Form 
{
	public function __construct($options = null) 
   { 
       parent::__construct($options);
       $this->setName('contact_us');
       
       $firstName = new Zend_Form_Element_Text('prenom');
       $firstName->setLabel('Prénom : ')
                 ->setRequired(true)
                 ->addValidator('NotEmpty');

       $lastName = new Zend_Form_Element_Text('nom');
       $lastName->setLabel('Nom : ')
                ->setRequired(true)
                ->addValidator('NotEmpty');
            
       $email = new Zend_Form_Element_Text('email');
       $email->setLabel('Email adresse : ')
             ->addFilter('StringToLower')
             ->setRequired(true)
             ->addValidator('NotEmpty', true)
             ->addValidator('EmailAddress'); 

       $message = new Zend_Form_Element_Textarea('message');
	   $message->setLabel('Message:')
            ->setRequired(true)
            ->addValidator('NotEmpty');
 
       $submit = new Zend_Form_Element_Submit('submit');
       $submit->setLabel('Contactez moi!');
       
       $this->addElements(array( $firstName,  $lastName, $email, $message, $submit));


       $this->clearDecorators();
	        $this->addDecorator('FormElements')
	         ->addDecorator('HtmlTag', array('tag' => '<ul>'))
	         ->addDecorator('Form');

	        $this->setElementDecorators(array(
	            array('ViewHelper'),
	            array('Errors'),
	            array('Description'),
	            array('Label', array('separator'=>' ')),
	            array('HtmlTag', array('tag' => 'li', 'class'=>'element-group')),
	        ));

	        // buttons do not need labels
	        $submit->setDecorators(array(
	            array('ViewHelper'),
	            array('Description'),
	            array('HtmlTag', array('tag' => 'li', 'class'=>'submit-group')),
	        ));
	
   }
}