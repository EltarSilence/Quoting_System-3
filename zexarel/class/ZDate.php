<?php

	class ZDate extends DateTime{
		
		public function __construct($init = null, $format = "Y-m-d"){
			parent::__construct();
			if(isset($init)){
				$d = date_create_from_format($format, $init);
				$this->setYear(date_format($d, "Y"));
				$this->setMonth(date_format($d, "m"));
				$this->setDay(date_format($d, "d"));
				$this->setHour(date_format($d, "H"));
				$this->setMinute(date_format($d, "i"));
				$this->setSecond(date_format($d, "s"));
			}
		}
		
		/*
			Aumenta la data, sono ammessi:
			 - YEAR
			 - MONTH
			 - WEEK
			 - DAY	
			 - HOUR
			 - MINUTE
			 - SECOND
		*/
		public function increment(int $q = 1, string $type = "day"){
			switch(strtolower ($type)){
				case "year":
				case "month":
				case "week":
				case "day":
				case "hour":
				case "minute":
				case "second":
					if($q < 0){
						$q = 1*$q;
					}
					if($q == 0){
						$q = 1;
					}
					parent::add(date_interval_create_from_date_string($q." ".strtolower($type)));
					break;
			}
		}
		
		/*
			Diminuisce la data, sono ammessi:
			 - YEAR
			 - MONTH
			 - WEEK
			 - DAY	
			 - HOUR
			 - MINUTE
			 - SECOND
		*/
		public function decrement(int $q = 1, string $type = "day"){
			switch(strtolower ($type)){
				case "year":
				case "month":
				case "week":
				case "day":
				case "hour":
				case "minute":
				case "second":
					if($q < 0){
						$q = 1*$q;
					}
					if($q == 0){
						$q = 1;
					}
					parent::sub(date_interval_create_from_date_string($q." ".strtolower($type)));
					break;
			}
		}
		
		
		public function getYear(){
			return intval(date("Y", parent::getTimestamp()));
		}
		public function getMonth(){
			return intval(date("m", parent::getTimestamp()));
		}
		public function getDay(){
			return intval(date("d", parent::getTimestamp()));
		}
		public function getHour(){
			return intval(date("H", parent::getTimestamp()));
		}
		public function getMinute(){
			return intval(date("i", parent::getTimestamp()));
		}
		public function getSecond(){
			return intval(date("s", parent::getTimestamp()));
		}
		
		public function setYear($year){
			parent::setDate($year, $this->getMonth(), $this->getDay());
		}
		public function setMonth($mouth){
			parent::setDate($this->getYear(), $mouth, $this->getDay());
		}
		public function setDay($day){
			parent::setDate($this->getYear(), $this->getMonth(), $day);
		}
		public function setHour($hour){
			parent::setTime($hour, $this->getMinute(), $this->getSecond());
		}
		public function setMinute($minute){
			parent::setTime($this->getHour(), $minute, $this->getSecond());
		}
		public function setSecond($second){
			parent::setTime($this->getHour(), $this->getMinute(), $second);
		}
		
		/*
			Metodo per comparare due date, ritorna:
			 - 1 se la prima data è maggiore della seconda
			 - -1 se la prima data è minore della seconda
			 - 0 se le date sono uguali
		*/
		/*
		public static compareTwoGDate($date1 = null, $date2 = null){
			if(isset($date1) && $date1 instanceof GDate &&
			   isset($date2) && $date2 instanceof GDate){
				if($date1->d  == $date2->d){
					return 0;
				}else if($date1->d  > $date2->d){
					return 1;
				}else{
					
					return -1;
				}	
			}else{
				return 0;
			}
		}
		*/
	}
?>