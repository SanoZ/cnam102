<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    
    public function _initPaginatorTemplate(){
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
    }
    
    /**
     * This function laods any general parameter taht the admin view may need, for example the user logged in
     */
    public function _initUserValues(){
        $view = Zend_Layout::getMvcInstance()->getView();
          
    } 
}
