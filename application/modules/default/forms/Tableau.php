<?php
class Form_Tableau extends Zend_Form
{
    public function init()
    {
        // La méthode HTTP d'envoi du formulaire
        $this->setMethod('post');

 		$this->addElement('hidden', 'article_id');


        $this->addElement('text', 'titre', array(
            'label'      => 'Titre :',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 20))
                )
        ));

	$this->addElement('text', 'description', array(
        'label'      => 'Description :',
        'required'   => true,
        'filters'    => array('StringTrim'),
        'validators' => array(
            array('validator' => 'StringLength', 'options' => array(0, 20))
            )
    ));

	$this->addElement('text', 'prix', array(
	    'label'      => 'Prix :',
	    'required'   => true,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('validator' => 'StringLength', 'options' => array(0, 20))
	        )
	));
	
	 // Add an metadescription element
        $this->addElement('select', 'theme_id', array(
            'label'      => 'Themes:',
            'required'   => true,
            'filters'    => array('StringTrim'),
        	'MultiOptions' 	 => $this->getThemes() 
        ));
 
	$this->addElement('checkbox', 'active', array(
	    'label'      => 'Active :',
		'checked'  => true
	));
  
	$this->addElement('checkbox', 'active', array(
	    'label'      => 'Active :',
		'checked'  => true
	));
 // Add an metadescription element
    $this->addElement('file', 'image', array(
        'label'      => 'Image:',
        'required'   => true,
        'destination' => 'tableaux',
		'validators' => array(
	        array('Size', false, array('min' => '0', 'max' => '5000000')),
	        array('Extension', false, 'jpeg,jpg,png,gif')
	    )  
    ));
 
     // Un bouton d'envoi
     $this->addElement('submit', 'submit', array(
         'ignore'   => true,
         'label'    => 'Ajouter',
     ));
	
	$this->setAttrib('enctype', 'multipart/form-data');

    }

	private function getThemes(){
    	$themes_model = new Model_ThemeMapper();
        $themes = $themes_model->fetchAll();

       	$array = array();
       	$nb = count ($themes);

       	for ($i = 0 ;$i < $nb ; $i++){
			$components[$i] = $themes[$i]->theme;
       	}

       	return $components;
    }
	 
}