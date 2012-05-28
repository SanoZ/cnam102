<?php

require_once('ApplicationController.php'); 
class WsClientController extends ApplicationController {
 
	public function indexAction() {
		// Récupération des 2 paramètres
		$a = $this->getRequest()->getParam('a');
		$b = $this->getRequest()->getParam('b');
 
		// Appel du WebService
		$client = new Zend_Soap_Client('http://ventes/ws/?wsdl');
		$result = $client->add($a, $b);
 
		// Passage des informations à la vue
		$this->view->a = $a;
		$this->view->b = $b;
		$this->view->result = $result;
	}
 
}
?>