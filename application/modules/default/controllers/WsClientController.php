<?php

require_once('ApplicationController.php'); 
class WsClientController extends ApplicationController {
 
	public function indexAction() {
		// Récupération des 2 paramètres
		$date = $this->getRequest()->getParam('date');
		$info = $this->getRequest()->getParam('info');
 		
		try{
			$client = new Zend_Soap_Client('http://ventes/ws/?wsdl'); 
			$result = $client->add($date, $info);
			
	 	} catch (SoapFault $s) {
		  die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
		} catch (Exception $e) {
		  die('ERROR: ' . $e->getMessage());
		} 
		// Passage des informations à la vue
		$this->view->date = $date;
		$this->view->info = $info;
		$this->view->result = $result;
	 
 }
}
?>