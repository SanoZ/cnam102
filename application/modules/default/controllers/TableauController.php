<?php
require_once('ApplicationController.php');
class TableauController extends ApplicationController
{

	public function preDispatch(){
	}
    public function init()
    {
        /* Initialize action controller here */
    }

	public function showAction(){
		
	}
	//list of all tableaux
    public function indexAction()
    {
		$tableaux = new Model_TableauMapper();
        $this->view->entries = $tableaux->fetchAll();
    }
	
	public function editAction(){
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector("index");
			exit;
		}
		
		$request = $this->getRequest();
        $form    = new Form_Tableau();
  

        if ($this->getRequest()->isPost()) {
        	if ($form->isValid($request->getPost())) {
	            $tableau = new Model_Tableau($form->getValues());
	            $mapper  = new Model_TableauMapper();
	            $mapper->save($tableau);
	            return $this->_helper->redirector('index');
	        }
       } else {
            $id = (int)$this->_request->getParam('id', 0);
            if ($id > 0) {
          		$tableau_model = new Model_Tableau();
				$tableaux = new Model_TableauMapper();
				$tableau = $tableaux->find($id,$tableau_model);
				if(!empty($tableau)){ 
					$form->populate($tableau->toArray());
				}
			}

        }
		$this->view->form = $form;
	}
	
	public function updateAction(){
		
	}

	//add a new tableau
 


}

