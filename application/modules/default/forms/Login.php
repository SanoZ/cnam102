<?php

class Frontend_Form_Login extends Zend_Form
{

    public function init()
    {
		$this->setName('login');
		$this->setAttrib('class', 'well');
		$this->setDecorators(array(
            'Description',
            'FormElements',
            'Form'
        ));
		$this->getDecorator('Description')->setOption('class', 'badge badge-warning');
		$this->setDescription("Vous avez déjà un compte ?");
		
        $mail = new Zend_Form_Element_Text('mail');
		$mail	->setLabel('adresse mail')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');
				
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('mot de passe')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
	
		$this->addElements(array($mail, $password, $submit));
		
		//$this->setElementDecorators(array(array('form', array('class'=>'well'))));
		//$this->setDecorators(array('FormElements',array('HtmlTag', array('tag' => 'table')),'Form',));
		
    }


}

