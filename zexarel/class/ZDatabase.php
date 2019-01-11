<?php
class ZDatabase{

    protected $user = "root";
    protected $password = "";
	protected $host = "localhost";
    protected $database = "test";

	private $conn;

	public function __construct() {
        $this->connect();
		$this->selectDatabase($this->database);
    }
	public function connect() {
        $this->conn = mysqli_connect($this->host, $this->user, $this->password);
    }
	public function selectDatabase($db){
		mysqli_select_db($this->conn, $db);
	}

	private $select = [];

	private $distinct = false;

	private $from = "";

	private $where = [];

	private $groupBy = [];

	private $orderBy = [];

	private $join = [];

	private $error = [];

	public function select(...$field ){
		foreach($field as $k => $v){
			array_push($this->select, $v);
		}
		return $this;
	}
	public function selectAll(){
		$this->select("*");
	}
	public function selectDistinct(...$field){
		$this->distinct = true;
		$this->select($field);
	}
	public function from($table){
		$this->from = $table;
		return $this;
    }
    public function where($field, $operator, $compare){
		if(!in_array($operator, ["=", ">", ">=", "<", "<=", "LIKE", "<>"])){
			array_push($this->error, "Errore nell'operatore del WHERE");
		}else{
			$a = $this->haveErrorChar($compare);
			if($a == false){
				array_push($this->error, "Errore nel campo di comparazione del WHERE");
			}else{
				if(gettype($compare) == 'string'){
					$a = "'".$a."'";
				}
				array_push($this->where, [$field, $operator, $a]);
			}
		}
		return $this;
    }
    public function groupBy($group_options){
        array_push($this->groupBy, $group_options);
		return $this;
    }
	public function orderBy($order_options){
		array_push($this->orderBy, $order_options);
		return $this;
    }
	public function innerJoin($table, $on, $operator, $compare){
		if(!in_array($operator, ["=", ">", ">=", "<", "<=", "LIKE", "<>"])){
			array_push($this->error, "Errore nell'operatore dell'INNER JOIN");
		}else{
			$a = $this->haveErrorChar($compare);
			if($a == false){
				array_push($this->error, "Errore nel campo di comparazione dell'INNER JOIN");
			}else{
				if(gettype($compare) == 'string'){
					if(strpos($a, ".") == false){
						$a = "'".$a."'";
					}
				}
				array_push($this->join, ["INNER JOIN", $table, $on, $operator, $a]);
			}
		}
		return $this;
	}
	public function leftJoin($table, $on, $operator, $compare){
		if(!in_array($operator, ["=", ">", ">=", "<", "<=", "LIKE", "<>"])){
			array_push($this->error, "Errore nell'operatore del LEFT JOIN");
		}else{
			$a = $this->haveErrorChar($compare);
			if($a == false){
				array_push($this->error, "Errore nel campo di comparazione dell'LEFT JOIN");
			}else{
				if(gettype($compare) == 'string'){
					if(strpos($a, ".") == false){
						$a = "'".$a."'";
					}
				}
				array_push($this->join, ["LEFT JOIN", $table, $on, $operator, $a]);

			}
		}
		return $this;
	}
	public function rightJoin($table, $on, $operator, $compare){
		if(!in_array($operator, ["=", ">", ">=", "<", "<=", "LIKE", "<>"])){
			array_push($this->error, "Errore nell'operatore del RIGHT JOIN");
		}else{
			$a = $this->haveErrorChar($compare);
			if($a == false){
				array_push($this->error, "Errore nel campo di comparazione dell'RIGHT JOIN");
			}else{
				if(gettype($compare) == 'string'){
					if(strpos($a, ".") == false){
						$a = "'".$a."'";
					}
				}
				array_push($this->join, ["RIGHT JOIN", $table, $on, $operator, $a]);

			}
		}
		return $this;
	}

	private $insert = [];

	private $into = "";

	private $value = [];

	public function insert($table, ...$field){
		$this->into = $table;
		foreach($field as $f){
			array_push($this->insert, $f);
		}
		return $this;
	}
	public function value(...$value){
		$v = [];
		foreach($value as $vv){
			$a = $this->haveErrorChar($vv);
			if($a == false){
				array_push($this->error, "Errore nel campo del VALUE");
				return $this;
			}else{
				array_push($v, $a);
			}
		}
		array_push($this->value, $v);
		return $this;
	}

	private $update = "";

	private $set = [];

	public function update($table){
		$this->update = $table;
		return $this;
	}
	public function set($field, $value){
		array_push($this->set, [$field, $value]);
		return $this;
	}

	public function getSQL(){
		if(sizeof($this->error) > 0){
			throw new DataException("There is some error", $this->error);
		}else{
			$sql = "";
			if(sizeof($this->select) > 0){
				if($this->from != ""){
					$sql = "SELECT ".($this->distinct ? "DISTINCT " : "").implode(", ", $this->select)." FROM ".$this->from;
					if(sizeof($this->join)){
						for($i = 0; $i < sizeof($this->join); $i++){
							$sql .=  " ".$this->join[$i][0]." ".$this->join[$i][1]." ON ".$this->join[$i][2]." ".$this->join[$i][3]." ".$this->join[$i][4];
						}
					}
					if(sizeof($this->where) > 0){
						for($i = 0; $i < sizeof($this->where); $i++){
							if($i == 0){
								$sql .= " WHERE";
							}
							$sql .= " ".implode(" ", $this->where[$i]);
							if($i < sizeof($this->where) - 1){
								$sql .= " AND";
							}
						}
					}
					if(sizeof($this->groupBy)){
						for($i = 0; $i < sizeof($this->groupBy); $i++){
							if($i == 0){
								$sql .= " GROUP BY";
							}
							$sql .=  " ".$this->groupBy[$i];
							if($i < sizeof($this->groupBy) - 1){
								$sql .= ",";
							}
						}
					}
					if(sizeof($this->orderBy)){
						for($i = 0; $i < sizeof($this->orderBy); $i++){
							if($i == 0){
								$sql .= " ORDER BY";
							}
							$sql .=  " ".$this->orderBy[$i];
							if($i < sizeof($this->orderBy) - 1){
								$sql .= ",";
							}
						}
					}
				}else{
					trigger_error("FROM non settato", E_USER_ERROR);
					exit();
				}
			}else if(sizeof($this->insert) > 0 && sizeof($this->value) > 0){
				$sql = "INSERT INTO ".$this->into;
				for($i = 0; $i < sizeof($this->insert); $i++){
					if($i == 0){
						$sql .= " (";
					}
					$sql .= $this->insert[$i].", ";
					if($i == sizeof($this->insert)-1){
						$sql = substr($sql, 0, strlen($sql)-2).")";
					}
				}
				$sql .= " VALUES ";
				for($i = 0; $i < sizeof($this->value); $i++){
					$sql .= "(";
					for($k = 0; $k < sizeof($this->value[$i]); $k++){
						$sql .= $this->value[$i][$k];
						if($k != sizeof($this->value[$i]) - 1){
							$sql .= ", ";
						}
					}
					$sql .= ")";
					if($i != sizeof($this->value) - 1){
						$sql .= ", ";
					}
				}
			}else if(sizeof($this->update) > 0 && sizeof($this->set) > 0){
				$sql = "UPDATE ".$this->update." SET";
				for($i = 0; $i < sizeof($this->set); $i++){
					$sql .= " ".implode(" ", $this->set[$i]);
					if($i != sizeof($this->set) - 1){
						$sql .= ", ";
					}
				}
				if(sizeof($this->where) > 0){
					for($i = 0; $i < sizeof($this->where); $i++){
						if($i == 0){
							$sql .= " WHERE";
						}
						$sql .= " ".implode(" ", $this->where[$i]);
						if($i < sizeof($this->where) - 1){
							$sql .= " AND";
						}
					}
				}
			}else{
				//possibile ??
			}
			return $sql;
		}
	}

	public function execute(){
		$sql = "";
		$ret = [];
		try{
			$sql = $this->getSQL();
			//d_var_dump($sql);

			$ret = $this->executeSql($sql);

		}catch(Exception $e){
			d_var_dump($e);
		}
		$this->select = [];
		$this->distinct = false;
		$this->from = "";
		$this->where = [];
		$this->groupBy = [];
		$this->orderBy = [];
		$this->join = [];
		$this->error = [];
		$this->insert = [];
		$this->into = "";
		$this->value = [];
		return $ret;
	}

	private function haveErrorChar($str){
		$p = ["--", ";", "({", "/*"];
		foreach($p as $pp){
			if(strpos($str, $pp) != false){
				return false;
			}
		}
		return str_replace("'", "\'", $str);

	}

	public function executeSql($sql){
        $result = mysqli_query($this->conn, $sql);
		$resultset = array();
		if(substr($sql, 0, 6) == "SELECT"){
			$fields = mysqli_num_fields($result);
			$type = [];
			for ($i = 0; $i < $fields; $i++) {
				$info = mysqli_fetch_field($result);
				switch($info->type){
					case 1 : //tinyint
					case 2 : //smallint
					case 3 : //int
					case 8 : //bigint
					case 9 : //mediumint
						$type[$info->name] = "int";
						break;
					case 4 : //float
					case 5 : //double
					case 246 : //decimal
						$type[$info->name] = "double";
						break;
					case 16: //bit
						$type[$info->name] = "bit";
						break;
					case 7 : //timestamp YYYYMMDDHHMMSS
					case 10 : //date YYYY-MM-DD
					case 11 : //time HH:MM:SS
					case 12 : //datetime YYYY-MM-DD HH:MM:SS
					case 252:
					case 253: //varchar
					case 254: //char
						$type[$info->name] = "string";
						break;
				}
			}
			while($row = mysqli_fetch_assoc($result)) {
				$r = $row;
				foreach($row as $k => $v){
					switch($type[$k]){
						case "bit":
							$r[$k] = ($v === 'true');
							break;
						case "int":
							$r[$k] = intval($v);
							break;
						case "double":
							$r[$k] = floatval($v);
							break;
					}
				}
				array_push($resultset, new ZRecord($r));
			}
		}
		if(!empty($resultset)){
            return $resultset;
		}else{
			return [];
		}
    }
}
?>
