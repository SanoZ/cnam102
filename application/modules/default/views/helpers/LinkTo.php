<?php

class Zend_View_Helper_LinkTo
{
	public function linkTo($action='index', $controller=null, $module=null, $params=null)
    {
        return App_Route::buildRoute($action, $controller, $module, $params);
	}
}
// class Zend_View_Helper_LinkTo
// {
//     protected static $baseUrl = null;
//     public function linkTo($path)
//     {
//         if (self::$baseUrl === null) {
//             $request = Zend_Controller_Front::getInstance()->getRequest();
//             $root = '/' . trim($request->getBaseUrl(), '/');
//             if ($root == '/') {
//                 $root = '';
//             }
//             self::$baseUrl = $root . '/';
//         }
//         return self::$baseUrl . ltrim($path, '/');
//     }
// }