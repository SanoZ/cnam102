<?php
require_once('ApplicationController.php');
class ThemeController extends ApplicationController
{

	public function preDispatch(){
        parent::preDispatch();
		if(empty($this->_loggedUser)  || $this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_redirect('index');
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
 
				$theme = new Model_Theme($form->getValues());
                $theme_mapper= new Model_ThemeMapper(); 
				$theme = $theme_mapper->find($id, $theme);
				
				if (!empty($theme)){ 
	 				$form->populate($theme->toArray() ); 
				}else{ 
					 $this->_helper->redirector("create");
				} 
				 
            }else{ 
				 $this->_helper->redirector("create");
			}
        }
 		$this->view->form =  $form;
	}
	
	
	public function updateAction(){
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector("index");
			exit;
		}
		//TODO
	}

	//add a new theme
	public function createAction(){
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector("index");
			exit;
		}
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
		if($this->_loggedUser->role_id != Model_Utilisateur::_ROLE_SUPER_ADMIN){
			$this->_helper->redirector("index");
			exit;
		}
	}
	
	


}

