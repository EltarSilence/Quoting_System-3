<?php
class ZRoute{

	private static $_listMethod = [];

	private static $_listUri = [];

	private static $_listCall = [];

	private static $_nameRoute = [];

	public static function get($uri, $function, $name = null){
		$uri = trim($uri, '/\^$');
		ZRoute::$_listMethod[] = "GET";
		ZRoute::$_listUri[] = $uri;
		ZRoute::$_listCall[] = $function;
		ZRoute::$_nameRoute[] = $name;
	}

	public static function post($uri, $function, $name = null){
		$uri = trim($uri, '/\^$');
		ZRoute::$_listMethod[] = "POST";
		ZRoute::$_listUri[] = $uri;
		ZRoute::$_listCall[] = $function;
		ZRoute::$_nameRoute[] = $name;
	}

	public static function getUri($name){
		for($i = 0; $i < sizeof(ZRoute::$_nameRoute); $i++){
			if(ZRoute::$_nameRoute[$i] == $name){
				return ZRoute::$_listUri[$i];
			}
		}
		return "";
	}

	public static function listen(){
		$uri = isset($_REQUEST['uri']) ? $_REQUEST['uri'] : '/';
		$uri = trim($uri, '/\^$');

		for($i = 0; $i < sizeof(ZRoute::$_listUri); $i++){
			if(preg_match("#^".ZRoute::$_listUri[$i]."$#", $uri)){
				if($_SERVER['REQUEST_METHOD'] == ZRoute::$_listMethod[$i]){
					$arr = array();
					switch($_SERVER['REQUEST_METHOD']){
						case "GET":
							$arr[0] = $_GET;
							break;
						case "POST":
							$arr[0] = $_POST;
							break;
					}
					call_user_func_array(ZRoute::$_listCall[$i], $arr);
					exit();
				}
			}
		}
		die($_SERVER['SERVER_PROTOCOL']." 404 Route Not Found");
	}

}
