<?php

class App_Route {
    
    public static function buildRoute($action='index', $controller=null, $module="default", $params=null, $uri ="")
	{
			if(empty($uri)){
        	$opts = array();

			$opts['action'] = $action;
			$opts['controller'] = $controller;
			$opts['module'] = $module;
			
			$param = "";
			if ($params){
				foreach ($params as $key => $value){
					$opts[$key] = $value;
					$param .= "/". $value;
				}
			}
			
			$url = "/".$controller;
			if ($action != 'index' ){
				$url .= "/".$action;
			} 
			if(!empty($param)){
				$url .= $param;
			}
	       
	        return $url;
		}else{
			return "/".$uri;
		}
    }


}