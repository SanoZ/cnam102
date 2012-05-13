<?php

require_once('ApplicationController.php');

class HomeController extends ApplicationController
{
    public function indexAction()
    {
        $_abTestVersion = self::getVariationForTest('new_homepage');
        if (1 === $_abTestVersion) {
            $this->_helper->viewRenderer('index-v2');
        } elseif (2 === $_abTestVersion) {
            $this->_helper->viewRenderer('index-v3');
        } elseif (3 === $_abTestVersion) {
            $this->_helper->viewRenderer('index-v4');
        }
        
        if (empty($this->_loggedUser)) {
            Zend_Layout::getMvcInstance()->setLayout('layout_newhome');
        } else {
            $this->_redirect('/stores/trending/fashion' );
        }
        $cache = new Model_Cache();
        $cache->cachingPages();
        
        $this->view->seo_title = "Discover new online retailers around the world.";
        $_ollio = 'http://' . $_SERVER['HTTP_HOST'];
        $this->view->facebook_url = $_ollio;
        $this->view->facebook_image = 'http://' . $_SERVER['HTTP_HOST'] . "/ollio.png";
        $this->view->facebook_title =   'Ollio';
    }
}
