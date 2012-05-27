<?php

require_once('ApplicationController.php');

class ContactController extends ApplicationController
{
	// public $_loggedUser;
    public function indexAction(){
		$this->view->pageTitle = "Contactez moi.";
		$this->view->bodyCopy = "<p >Remplissez le formulaire.</p>";
  
	
		$form = new Form_Contact();

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                return $this->_helper->redirector('merci'); 
            } else {
                $form->populate($formData);
            }
        }

        $this->view->form = $form;
    }
 	
	public function merciAction(){
		
	}
}