<?php
// require_once('Admin_ApplicationController.php');
class ThemeController extends Zend_Controller_Action
{

	public function preDispatch(){
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector("index");
			exit;
		}
	}
    public function init()
    {
        /* Initialize action controller here */
    }

	public function showAction(){
		
	}
	//list of all themex
    public function indexAction()
    {
		$theme = new Model_ThemeMapper();
        $this->view->entries = $theme->fetchAll();
    }
	
	public function editAction(){
		$request = $this->getRequest();
        $form    = new Form_Theme();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $theme = new Model_Theme($form->getValues());
                $mapper  = new Model_ThemeMapper();
                $mapper->save($theme);
                return $this->_helper->redirector('index');
            }
        }
		else {
            $id = (int)$this->_request->getParam('id', 0);
            if ($id > 0) {
                $tableaux = new Model_Tableau();
                $tableau = $tableaux->fetchRow('article_id='.$id);
                $form->populate($tableau->toArray());
            }
        }
	}
	public function updateAction(){
		
	}

	//add a new theme
	public function createAction(){
        $request = $this->getRequest();
        $form    = new Form_Theme();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $theme = new Model_Theme($form->getValues());
                $mapper  = new Model_ThemeMapper();
                $mapper->save($theme);
                return $this->_helper->redirector('index');
            }
        }

        $this->view->form = $form;
	}
	
	//delete 
	public function deleteAction(){
		
	}
	
	


}

