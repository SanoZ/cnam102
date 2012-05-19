<?php

require_once('ApplicationController.php');

class AuthController extends ApplicationController
{
	public function init() {
  }

	public function indexAction()
	{
		$form = new Form_Login();
$this->view->message = "";
		$request = $this->getRequest();
		if ($request->isPost()) { 
			if ($form->isValid($request->getPost())) {
				if ($this->_process($form->getValues())) {
					// We're authenticated! Redirect to the home page
					$this->_helper->redirector('index', 'index');
				}else{
					$this->view->message = "error";
				}
			}
		}
			$this->view->form = $form;
	}
	
	protected function _process($values)
	{
     // Get our authentication adapter and check credentials
     $adapter = $this->_getAuthAdapter();
     $adapter->setIdentity($values['mail']); 
     $adapter->setCredential($values['password']);

     $auth = Zend_Auth::getInstance();
     $result = $auth->authenticate($adapter);
     if ($result->isValid()) {
         $user = $adapter->getResultRowObject();
         $auth->getStorage()->write($user);
         return true;
     }
     return false;
	}
	
	protected function _getAuthAdapter() {

		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

		$authAdapter->setTableName('utilisateurs')
		    ->setIdentityColumn('email')
		    ->setCredentialColumn('password')
		    ->setCredentialTreatment('SHA1(CONCAT(?,salt))');
 
		return $authAdapter;
	}
	
	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index'); // back to login page
	}
}