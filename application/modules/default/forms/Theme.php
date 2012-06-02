<?php
class Form_Theme extends Zend_Form
{
    public function init()
    {
        // La méthode HTTP d'envoi du formulaire
        $this->setMethod('post');

 		$this->addElement('hidden', 'article_id');
 
        $this->addElement('text', 'theme', array(
            'label'      => 'Thème :',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));
 
		$this->addElement('checkbox', 'active', array(
		    'label'      => 'Active :',
			'checked'  => true
		));
 
        // Un bouton d'envoi
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Ajouter',
        ));

    }
}