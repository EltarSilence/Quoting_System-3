<?php

class Controller{

	public static function checkAndPay(){
		$db = new DB();
		$ret = $db->select('*')
			->from('scommessas')
			->where('pagataS', '=', 0)
			->execute();
		foreach ($ret as $k => $v) {
			$w = Controller::isWon($v);
			if($w == 0){
				$db->update('scommessas')
					->set('pagataS', 1)
					->where('idS', '=', $v->idS)
					->execute();
			}elseif($w > 0){
				$ret = $db->select('*')
					->from('users')
					->where('id', '=', $v->idUtenteS)
					->execute()[0];

				$db->update('users')
					->set('coin', $ret->coin + $w)
					->where('id', '=', $v->idUtenteS)
					->execute();

				$db->update('scommessas')
					->set('pagataS', 1)
					->where('idS', '=', $v->idS)
					->execute();
			}
		}
	}

	public static function login($data){
		$fv = new ZFormValidator();
		$fv->addField(new Field('email', 'required', 'email'));
		$fv->addField(new Field('password', 'required'));
		if($fv->isValid($data)){
			$db = new DB();
			$u = $db->select("*")
				->from("users")
				->where("email", "=", $data['email'])
				->where("password", "=", $data['password'])
				->execute();
			if(sizeof($u) == 1){
				ZAuth::createObject("user");
				ZAuth::user()->id = $u[0]->id;
				ZAuth::user()->name = $u[0]->name;
				ZAuth::user()->coin = $u[0]->coin;
				header("Location: home");
			}else{
				header("Location: login?er=er");
			}
		}
	}

	public static function logout(){
		ZAuth::destroyObject("user");
		header("Location: home");
	}

	public static function register($data){
		$isPwdOk = false;
		$fv = new ZFormValidator();
		$fv->addField(new Field('username', 'required'));
		$fv->addField(new Field('email', 'required', 'email'));
		$fv->addField(new Field('password', 'required'));
		$fv->addField(new Field('password_confirmation', 'required'));
		if ($data['password_confirmation'] == $data['password']){
			$isPwdOk = true;
		}
		if ($fv->isValid($data) && $isPwdOk){
			$db = new DB();
			$sudo = $db->insert("users", "name", "email", "password", "coin")
				->value("'".$data['username']."'", "'".$data['email']."'", "'".$data['password']."'", 5000)
				->getSql();
			$sudo = $db->executeSql('INSERT INTO users (name, email, password, coin) VALUES ("'.$data['username'].'" , "'.$data['email'].'", "'.$data['password'].'", 5000)');
		}
	}

  public static function myBet(){
      $userBets = Controller::getAllBetsBy(ZAuth::user()->id);
      $scommesse = array();

      for ($i = 0; $i < count($userBets); $i++){
        $scommessa = array();

        $scommessa["puntata"] = $userBets[$i]->coinS;
        $scommessa["data"] = $userBets[$i]->dataS;
        $scommessa["id"] = $userBets[$i]->idS;
        $scommessa['quotaFinale'] = 1;

        $mul = Controller::getBetDetail($userBets[$i]);

        $multiple = array();
				$ww = 1;
        for($k = 0; $k < count($mul); $k++){
          $multipla = array();
          $multipla["id"] = $mul[$k]->idScommessaM;
          $multipla["chiave"] = $mul[$k]->chiaveM;
          $multipla["tipo"] = $mul[$k]->tipoM;
          $multipla["value"] = $mul[$k]->valueM;
          $multipla["quota"] = $mul[$k]->quotaM;

          $scommessa['quotaFinale'] *= $mul[$k]->quotaM;

          $multipla["risultato"] = $mul[$k]->risultatoR;
          $multipla["descrizione"] = $mul[$k]->descrizioneD;

          $multipla["isWon"] = Controller::isMultiplaWon($mul[$k]);
					$ww *= $multipla["isWon"];
          array_push($multiple, $multipla);
        }
        $scommessa["multiple"] = $multiple;

				$scommessa["isWon"] = ($userBets[$i]->pagataS == 0 ? -1 : $ww);

        array_push($scommesse, $scommessa);
      }
      return $scommesse;
  }

  public static function getDisponibili(){
		$db = new DB();
    $verifiche = $db->select('*')
			->from('disponibilis')
			->where('dalD', '<=', date('Y-m-d'))
    	->where('alD', '>=', date('Y-m-d'))
    	->execute();
    $ret = array();
    foreach ($verifiche as $v) {
      $a = array();
      $a['alD'] = $v->alD;
      $a['typeD'] = $v->typeD;
      $a['fileD'] = $v->fileD;
      $a['descrizioneD'] = explode("|", $v->descrizioneD);

      array_push($ret, $a);
    }

    return json_encode($ret);
  }

  public static function getScommessa($data){
		$db = new DB();
		$key = $data['scommessa'];

    $scom = $db->select("*")
			->from("disponibilis")
      ->where('typeD', '=', $key)
      ->execute();

    $data = array();
    $data['type'] = $scom[0]->typeD;
    $data['descrizione'] = explode("|", $scom[0]->descrizioneD);
    $data['al'] = date("d/m/Y", strtotime($scom[0]->alD));
    $data['filename'] = $key;
    $data['file'] = json_decode($scom[0]->fileD, true);

    return json_encode($data);
  }

  public static function getWeekWin(){
		$db = new DB();
    $ret = array();
    $scom = $db
		->select("scommessas.*", "users.*")
		->from("scommessas")
		->innerJoin('users', 'users.id', '=', 'scommessas.idUtenteS')
		->where('dataS', '>=', date("Y-m-d", strtotime(date("Y-m-d")."-7day")))
		->where('pagataS', '=', 1)
		->execute();
		if(sizeof($scom) > 0){
			foreach ($scom as $s) {
			  $winCoin = Controller::isWon($s);
			  if($winCoin > 0){
				array_push($ret, array($s->name => $winCoin));
			  }
			}
		}
    usort($ret, "self::cmp");
    return $ret;
  }

  public static function getMouthWin(){
    $db = new DB();
		$ret = array();
    $scom = $db
			->select("scommessas.*", "users.*")
			->from("scommessas")
			->innerJoin('users', 'users.id', '=', 'scommessas.idUtenteS')
			->where('dataS', '>=', date("Y-m-d", strtotime(date("Y-m-d")."-30day")))
			->where('pagataS', '=', 1)
			->execute();
		if(sizeof($scom) > 0){
			foreach ($scom as $s) {
			  $winCoin = Controller::isWon($s);
			  if($winCoin > 0){
				array_push($ret, array($s->name => $winCoin));
			  }
			}
		}
    usort($ret, "self::cmp");
    return $ret;
  }

  public static function cmp($a, $b){
    foreach($a as $akey => $avalue){
      foreach($b as $bkey => $bvalue){
        return ($avalue < $bvalue) ? 1 : -1;
      }
    }
    return 0;
  }

  private static function isWon($scommessa){
    $db = new DB();
		$vin = 1;
    $mult = $db->select("risultatis.*", "multiplas.*")
			->from("multiplas")
			->leftJoin('risultatis', 'multiplas.chiaveM', '=', 'risultatis.chiaveR')
			->where('idScommessaM', '=', $scommessa->idS)
			->execute();
		if(sizeof($mult) > 0){
			foreach ($mult as $m) {
			  if($vin == 0){
					return 0;
			  }
			  if($vin < 0){
					return -1;
			  }
			  $vin *= Controller::isMultiplaWon($m);
			}
		}else{
			return 0;
		}
    return $vin*$scommessa->coinS;
  }

  private static function isMultiplaWon($m){
    $vin = 1;
    if ($m->chiaveR == ""){
      return -1;
    }
    $chiave = explode('_', $m->chiaveR);
    $tipo = $chiave[0];
    switch ($tipo) {
      case 'EUO':
        $cat = $m->tipoM;
        switch ($cat){
          case 'ESATTO':
			  		if ($m->valueM == $m->risultatoR){
							return $vin = $vin*$m->quotaM;
			  		}else {
							return 0;
			  		}
			  		break;
				  case 'UNDER':
				  	$value = floatval($m->valueM);
				  	$res = floatval($m->risultatoR);
				  	if ($res < $value) {
							return $vin *= $m->quotaM;
				  	}else {
							return 0;
				  	}
				  	break;
				case 'OVER':
				  $value = floatval($m->valueM);
				  $res = floatval($m->risultatoR);

				  if ($res > $value) {
						return $vin *= $m->quotaM;
				  }else {
						return 0;
				  }
				  break;
				default:
				  return 0;
				  break;
			  }
		  	break;
    	case 'SN':
      case 'MT':
		  	if ($m->risultatoR == $m->valueM){
					return $vin = $vin*$m->quotaM;
		  	}else {
					return 0;
		  	}
		  	break;
      default:
				return 0;
    	break;
    }
  }

  public static function getAllBetsBy($id){
		$db = new DB();
		$bets = $db->select("*")
			->from("scommessas")
			->where('idUtenteS', '=', $id)
    	->orderBy('idS', 'desc')
    	->execute();
    return $bets;
  }

  public static function getBetDetail($scommessa){
		$db = new DB();
		$mult = $db->select('risultatis.*', 'multiplas.*', 'disponibilis.*')
			->from('multiplas')
			->leftJoin('risultatis', 'multiplas.chiaveM', '=', 'risultatis.chiaveR')
			->innerJoin('disponibilis', 'multiplas.chiaveM', 'LIKE', 'CONCAT(disponibilis.typeD, "%")')
      ->where('idScommessaM', '=', $scommessa->idS)
      ->execute();
    return $mult;
  }

  public static function addScommessa($data){
		if(ZAuth::user() != false){
			$fv = new ZFormValidator();
			$fv->addField(new Field('chiave', 'required'));
			$fv->addField(new Field('type', 'required'));
			$fv->addField(new Field('value', 'required'));
			$fv->addField(new Field('importo', 'required', 'number', '<=', ZAuth::user()->coin - 100));
			if($fv->isValid($data)){
				$db = new DB();

				$db->insert("scommessas", "idUtenteS", "coinS", "dataS", "pagataS")
					->value(ZAuth::user()->id, $data['importo'], '"'.date('Y-m-d').'"', 0)
					->execute();

				$db->update("users")
					->set("coin", ZAuth::user()->coin - $data['importo'])
					->where("id", '=', ZAuth::user()->id)
					->execute();

				$ret = $db->select('*')
					->from('users')
					->where('id', '=', ZAuth::user()->id)
					->execute();

				ZAuth::user()->coin = $ret[0]->coin;

				$id = $db->select('MAX(idS) AS idS')
					->from("scommessas")
					->execute()[0]->idS;

	      for($i = 0; $i < sizeof($data['chiave']); $i++){
					$quotaM = 0;

					$t = explode("_", $data['chiave'][$i]);
					$d = $db->selectAll()
						->from('disponibilis')
						->where('typeD', '=', $t[0]."_".$t[1])
						->execute();
					$j = json_decode($d[0]->fileD, true);
					switch($t[0]){
	          case "EUO":
	            $quotaM = $j[$t[2]][$data['type'][$i]][$data['value'][$i]];
	            break;
	          case "SN":
	            $quotaM = $j[$t[2]][$data['value'][$i]];
	            break;
	          case "MT":
	            $quotaM = $j[explode("-", $data['value'][$i])[0]]['quota'];
	            break;
	        }
					$db->insert('multiplas', 'idScommessaM', 'chiaveM', 'tipoM', 'valueM', 'quotaM')
						->value($id, '"'.$data['chiave'][$i].'"', '"'.(strpos($data['chiave'][$i], 'EUO_') !== false ? $data['type'][$i] : "").'"', '"'.$data['value'][$i].'"', $quotaM)
						->execute();
	      }
	      return header("Location: home");
	    }else{

	    }
	  }
	}

}
