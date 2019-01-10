<?php
class ZRecord{
	
	private $property;
	
	public function __construct($data){
		foreach($data as $k => $v){		
			$this->property[$k] = $v;
		}
	}
	
	public function __get($name){
		if(isset($this->property[$name])){
			return $this->property[$name];
		}else{
			return "";
		}
	}
	
	public function __set($name, $value){
		switch(gettype($this->property[$name])){
			case "integer":
				$this->property[$name] = intval($value);
				break;
			case "float":
				$this->property[$name] = floatval($value);
				break;
			case "bool":
				$this->property[$name] = boolval($value);
				break;
			case "date":
				$this->property[$name] = date("Y-m-d", strtotime($value));
				break;
			case "string":
				$this->property[$name] = "".$value;	
				break;
		}
	}
	
	
}
?>