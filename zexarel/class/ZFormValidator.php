<?php

	class ZFormValidator{

		private $error;

		private $field;

		public function __construct(){
			$this->error = [];
			$this->field = [];
		}

/*
VALIDATOR:
	-is a file/file type
		-is multiple
		-is .png
		-is .jpg
		...
		..
	-is a date
		-date >
		-date <
		..
		..
	-is checkbox
		-with compare selected value
	-is a string
		-strlen > compare
		-strlen >= compare
		-strlen < compare
		-strlen <= compare
		-strlen = compare
		-strlen != compare
	-is a number
		-=Compare
		->=Compare
		-<=Compare
		->Compare
		-<Compare
		-!=Compare
	-is a range
		-min
		-max
		-step
		-value
	-is a telephone
*/


		public function addField($val){
			array_push($this->field, $val);
		}

		public function getErrors(){
			return $this->error;
		}
		public function isValid($data){
			$valid = true;
			foreach($this->field as $field){
				if(!$field->isValidWhit($data)){
					$valid = false;
				}
			}
			return $valid;
		}



	}

	class Field{
		public $field_name;
		public $required = false;
		public $type;
		public $operator = [];
		public $compare = [];

		public function __construct(...$variable){
			$this->field_name = $variable[0];
			for($i = 1; $i < sizeof($variable); $i++){
				switch(strtolower($variable[$i])){
					case "req":
					case "required":
					case "set":
						$this->required = true;
						break;
					case "number":
					case "email":
					case "color":
					case "date":
					case "file":
						$this->type = strtolower($variable[$i]);
						break;
					case "=":
					case "!=":
					case ">":
					case "<":
					case ">=":
					case "<=":
						if(
							isset($variable[$i + 1]) &&
							strtolower($variable[$i + 1]) != "req" &&
							strtolower($variable[$i + 1]) != "required" &&
							strtolower($variable[$i + 1]) != "set" &&
							strtolower($variable[$i + 1]) != "email" &&
							strtolower($variable[$i + 1]) != "color" &&
							strtolower($variable[$i + 1]) != "date" &&
							strtolower($variable[$i + 1]) != "file" &&
							strtolower($variable[$i + 1]) != "=" &&
							strtolower($variable[$i + 1]) != "!=" &&
							strtolower($variable[$i + 1]) != ">" &&
							strtolower($variable[$i + 1]) != "<" &&
							strtolower($variable[$i + 1]) != ">=" &&
							strtolower($variable[$i + 1]) != "<="
						){
							array_push($this->operator, strtolower($variable[$i]));
							array_push($this->compare, $variable[$i + 1]);
							$i++;
						}else{
							//add error
						}
						break;
					default:
						$this->type = "string";
						break;
				}
			}
		}

		public function isValidWhit($data){
			if(array_key_exists($this->field_name, $data)){
				$ret = true;
				if($this->required){
					if(!isset($data[$this->field_name]) || $data[$this->field_name] == ""){
						$ret = false;
						//add error
					}
				}
				if($ret){
					switch($this->type){
						case "email":
							if(preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/", $data[$this->field_name]) == false){
								$ret = false;
								//add error
							}
							break;
						case "color":
							if(preg_match("/#[0-9a-zA-Z]{6}/", $data[$this->field_name]) == false){
								$ret = false;
								//add error
							}
							break;
						case "date":
							if(preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $data[$this->field_name]) == false){
								$ret = false;
								//add error
							}
							break;
						default:
							break;
					}
				}
				if($ret){
					for($i = 0; $i < sizeof($this->operator); $i++){
						switch($this->type){
							case "date":
								$a = strtotime($data[$this->field_name]).$this->operator[$i].strtotime($this->compare[$i]);
								if(!eval("if($a){ return true;}else{ return false;}")){
									$ret = false;
								}
								break;
							default:
								$a = $data[$this->field_name].$this->operator[$i].$this->compare[$i];
								if(!eval("if($a){ return true;}else{ return false;}")){
									$ret = false;
								}
								break;
						}
					}
				}
			}else{
				return false;
			}
			return $ret;
		}

	}

?>
