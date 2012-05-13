<?php

class Default_CompteController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
		//$this->view->bodyClass = 'home';
		$loginForm = new Frontend_Form_Login();
		
		$this->view->loginForm = $loginForm;
		
		if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();

            if ($loginForm->isValid($formData)) {

                $mail = $loginForm->getValue('mail');
                $password = $loginForm->getValue('password');
			}
		}

		
    }


}

