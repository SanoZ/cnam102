<?php

class PanierController extends Zend_Controller_Action
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
	$this->getPanier()->ajouterArticle($this->getRequest()->getParam('id'));
	$this->_helper->redirector('index');
	}
	
	public function supprimerAction()
	{
	$this->getPanier()->supprimerArticle($this->getRequest()->getParam('id'));
	$this->_helper->redirector('index');
	}

	public function getPanier() {
        $session = Zend_Registry::get('session');
        return $session->panier;
    }
}


