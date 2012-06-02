<?php

require_once('ApplicationController.php'); 
class PanierController extends ApplicationController
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$this->view->panier = $this->getPanier();
    }
	
	public function ajouterAction()
	{ 
		if(empty($this->loggedUser)){
			echo $this->navigation()->menu()->render($this->admin);
		}
		$this->getPanier()->ajouterArticle($this->getRequest()->getParam('id'));
		$this->_helper->redirector('index');
	}
	
	public function supprimerAction()
	{
		$this->getPanier()->supprimerArticle($this->getRequest()->getParam('id'));
		$this->_helper->redirector('index');
	}

 public function paiementAction() {
        $form = new Model_Form_Paiement();
        $this->view->form = $form;
        if ($this->_request->isPost ()) {
            $formData = $this->_request->getPost ();
            if ($form->isValid ( $formData )) {
                $figlet = new Zend_Text_Figlet();
                $this->view->form = '<pre>'.$figlet->render('Wahoooo').'</pre>';
                $mail = new Zend_Mail();
                $auth = Zend_Auth::getInstance();
                $email = $auth->getIdentity()->email;
                $mail->setBodyHtml('Nous avons bien pris en compte votre commande,
                    elle sera traité dans les plus bref delai');
                $mail->setFrom($email, 'TP1-Ecommerce');
                $mail->addTo($email);
                $mail->setSubject('Commande sur TP1-Ecommerce');

                //$mail->send();


            }
        }
    }

}


