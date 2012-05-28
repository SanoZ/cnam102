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
					$test = array(
					    'article_id' => 1,
					    'description' =>  'testing' ,
					    'titre' =>  'titree',
					    'theme_id' => null,
					    'format_id' => null,
					    'prix' => null,
					    'date_publication' =>  '0000-00-00 00:00:00' ,
					    'statut_id' => null,
					    'date_modification' => null,
					    'stock' => null,
					'active' => null
					);
					$form->populate($test);
				}
			}

        }		// 
        		// 
        		        $this->view->form = $form;
        		// echo "l;;j;";
	}
	
	public function updateAction(){
		
	}

	//add a new tableau
	public function createAction(){
        $request = $this->getRequest();
        $form    = new Form_Tableau();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) { 
	
                $tableau = new Model_Tableau($form->getValues());
				if ($form->image->isUploaded()) {
					$mapper  = new Model_TableauMapper();
					$mapper->save($tableau);	
	                return $this->_helper->redirector('index'); 
                
				}else{
				    echo "erreur avec l'image";die;
				}
            }
        }

        $this->view->form = $form;
	}
	
	//delete 
	public function deleteAction(){
		
	}
	
	
	public function nouveauteAction(){
		
	}
	


}

