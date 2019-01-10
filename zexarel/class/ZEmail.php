<?php
	class ZEmail{

		private $reciver = [];
	
		private $object = "";
		
		private $body = "";
		
		private $header = [];
		
		public function __construct(){
		}
		
		public function addReciver($reciver){
			array_push($this->reciver, $reciver);
			return $this;
		}
		
		public function setObject($object){
			$this->object = $object;
			return $this;
		}
		
		public function setBody($body){
			$this->body = $body;
			return $this;
		}
		
		public function addHeader($key, $value){
			$this->header[$key] = $value;
			return $this;
		}
		
		protected function ifSend(){
		}
		
		protected function ifNotSend(){
		}
		
		public function send(){
			if(mail(implode(", ", $this->reciver), $this->object, $this->body, implode("\r\n", $this->header))){
				$this->ifSend();
				return true;
			}else{
				$this->ifNotSend();
				return false;
			}
		}
	}
?>