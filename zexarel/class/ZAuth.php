<?php
	session_start();
	class ZAuth{

		public static function __callStatic($name, $arguments){
			if(!isset($_SESSION[$name])){
				return false;
			}
			return $_SESSION[$name];
		}

		public static function createObject($name){
			if(!isset($_SESSION[$name])){
				$_SESSION[$name] = new ZObject();
			}
		}

		public static function destroyObject($name){
			if(isset($_SESSION[$name])){
				$_SESSION[$name] = null;
			}
		}
	}

	class ZObject{

		private $property = [];

		public function __get($name){
			if(isset($this->property[$name])){
				return $this->property[$name];
			}else{
				return "";
			}
		}

		public function __set($name, $value){
			$this->property[$name] = $value;
		}

	}

?>
