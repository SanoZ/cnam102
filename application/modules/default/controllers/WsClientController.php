<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Soap_Client');
 require_once('ApplicationController.php'); 

class WsClientController extends ApplicationController  {
 
	public function indexAction() {
		$art_id = $this->getRequest()->getParam('id'); 
 		
		try{
			$client = new Zend_Soap_Client('http://ventes/ws/?wsdl'); 
			$result = $client->getAllTableaux();
		  
	 	} catch (SoapFault $s) {
		  die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
		} catch (Exception $e) {
		  die('ERROR: ' . $e->getMessage());
		} 
		// Passage des informations à la vue 
		$this->view->result = $result;
	 
 }
}
 ?>
