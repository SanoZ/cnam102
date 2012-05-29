<?php
require_once('services/WebService.php');
 
require_once('ApplicationController.php'); 
class WsController extends ApplicationController {
 
	public function indexAction() {
		$this->getHelper('viewRenderer')->setNoRender(true);
		
		if (is_null($this->getRequest()->getParam('wsdl'))) {
			// Traitement de la requête
			$server = new Zend_Soap_Server('http://ventes/ws/?wsdl');
			$server->setClass('WebService');
			$server->handle();
		} else {
			// Retour de la WSDL
			$wsdl = new Zend_Soap_AutoDiscover();
			$wsdl->setClass('WebService');
			$wsdl->handle();
		}
		exit;
	}
 
}
?>