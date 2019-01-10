<?php

abstract class ZToken{
	
    protected $key;
	
    private $iv = "Zexal0807TheBest";		
	//if you use 'AES-256-CFB8' algorithm the iv must be 16 bytes, 
	//if you change the algorithm, check the lenght of iv
	
	protected $property = [];
	
	protected $validity = "1 day";
	
	private $values = [];
	
	public function __construct($token = null){
		foreach($this->property as $p){
			$this->values[$p] = null;
		}
		$this->values["Zvalidity"] = ((new DateTime(date("Y-m-d H:i:s")))->add(date_interval_create_from_date_string($this->validity)))->getTimestamp();
		if(isset($token)){
			$vs = explode("|",$this->decrypt($token));
			foreach($vs as $v){
				$a = explode("=", $v);
				$this->values[$a[0]] = $a[1];
			}
		}
	}
	
	private function encrypt($text) {
		$en = openssl_encrypt($text, "AES-256-CFB8", $this->key, 0 , $this->iv);
		$en = str_split($en);
		$ret = "";
		for($i = 0; $i < sizeof($en); $i++){
			$ret .= $en[$i];
			$ret .= chr(ord($en[$i])+8);
		}
		return $ret;
    }
    
	private function decrypt($encrypted_text){
		$en = str_split($encrypted_text);
		$ret = "";
		for($i = 0; $i < sizeof($en); $i += 2){
			$ret .= $en[$i];
		}
		return openssl_decrypt($ret, "AES-256-CFB8" , $this->key, 0, $this->iv);
    }
	
	public function __get($name){
		if(array_key_exists($name, $this->values)){
			return $this->values[$name];
		}
	}
	
	public function __set($name, $value){
		if(array_key_exists($name, $this->values)){
			$this->values[$name] = $value;
		}
	}
		
    public function isValid(){
		if(strtotime(date("Y-m-d H:i:s")) <= intval($this->values['Zvalidity'])){
			return true;
		}else{
			return false;
		}
    }

    public function generateToken(){
		$t = [];
		foreach($this->values as $k => $v){
			array_push($t, $k."=".$v);
		}
		$t = implode("|", $t);
		return $this->encrypt($t);
    }

}