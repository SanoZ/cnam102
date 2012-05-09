<?php

abstract class ApplicationController extends Zend_Controller_Action
{
    protected $_checkActionCookies = true;
    protected $_loggedUser;
    
    /**
     * @return Zend_Cache
     */
    static public function getCache($type = 'general')
    {
        $_manager = new Zend_Cache_Manager();
        $_cache = $_manager->getCache($type);
        // if (empty($_cache)) {
        //             
        //             // Can be switched to memcached later
        //             if ('general' == $type) {
        //                 $_cache = Zend_Cache::factory('Core', 'File', array(
        //                         'lifetime'                  => 60 * 5,
        //                         'automatic_serialization'   => true
        //                     ), array(
        //                         'cache_dir'                 => '/tmp'
        //                     )
        //                 );
        //                 
        //             // Strict for file cache
        //             } elseif ('file' == $type) {
        //                 $_cache = Zend_Cache::factory('Core', 'File', array(
        //                         'lifetime'                  => 60 * 5,
        //                         'automatic_serialization'   => true
        //                     ), array(
        //                         'cache_dir'                 => '/tmp'
        //                     )
        //                 );
        //             }
        //             
        //             $_manager->setCache($type, $_cache);
        // }
        // return $_cache;
    }
   
    
    static public function getTestCookieName($testName)
    {
        // return 't_' . substr(md5($testName), 0, 5);
    }
    
    public function preDispatch()
    {
        parent::preDispatch();
        // Zend_Db_Table_Abstract::setDefaultMetadataCache(self::getCache());
        //        
        //        if (!defined('__RELEASE_HASH')) {
        //            define('__RELEASE_HASH', substr(md5('ollio'), 0, 10));
        //        }
        //        
        //        if (!defined('__SITE_URL')) {
        //            define('__SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
        //        }
        //        
        //        $_userModel = new Model_User();
        //        if (true === $_userModel->getCurrentUser()) {
        //            $this->view->loggedUser = $this->_loggedUser = $_userModel;
        //        }
        //        
        //        if (true === $this->_checkActionCookies) {
        //            $this->_parseActionCookies();
        //        }
    }
    
    
}
