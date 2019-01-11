<?php

class Controller{

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

        $scommessa["isWon"] = Controller::isWon($userBets[$i]);
        $scommessa["puntata"] = $userBets[$i]->coinS;
        $scommessa["data"] = $userBets[$i]->dataS;
        $scommessa["id"] = $userBets[$i]->idS;
        $scommessa['quotaFinale'] = 1;

        $mul = Controller::getBetDetail($userBets[$i]);

        $multiple = array();
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

          array_push($multiple, $multipla);
        }
        $scommessa["multiple"] = $multiple;
        array_push($scommesse, $scommessa);
      }
      return $scommesse;
  }
/*
  public static function getDisponibili(){
    $verifiche = Disponibili::whereDate('dalD', '<=', date('Y-m-d'))
    ->whereDate('alD', '>=', date('Y-m-d'))
    ->get();
    $ret = array();
    foreach ($verifiche as $v) {
      $a = array();
      $a['alD'] = $v->alD;
      $a['typeD'] = $v->typeD;
      $a['fileD'] = $v->fileD;
      $a['descrizioneD'] = explode("|", $v->descrizioneD);

      array_push($ret, $a);
    }

    return $ret;
  }

  public static function getScommessa(){
    $key = Input::get('scommessa');

    $scom = Disponibili
      ::where('typeD', '=', $key)
      ->get();

    $data = array();
    $data['type'] = $scom[0]->typeD;
    $data['descrizione'] = explode("|", $scom[0]->descrizioneD);
    $data['al'] = date("d/m/Y", strtotime($scom[0]->alD));
    $data['filename'] = $key;
    $data['file'] = json_decode($scom[0]->fileD, true);

    return $data;
  }
*/
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
    if (!isset($m->chiaveR)){
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
/*
  public static function addScommessa(){
    if(Auth::user()->coin >= Input::get('importo') + 100){
      $a = new Scommessa;
      $a->idUtenteS = Auth::user()->id;
      $a->coinS = Input::get('importo');
      $a->dataS = date('Y-m-d');
      $a->pagataS = 0;
      $a->save();
      $ut = Use1r
        ::where('id', '=', Auth::user()->id)
        ->update(['coin' => Auth::user()->coin - Input::get('importo')]);
      $id = $a->id;
      for($i = 0; $i < sizeof(Input::get('chiave')); $i++){
        $b = new Multipla;
        $b->idScommessaM = $id;
        $b->chiaveM = Input::get('chiave')[$i];
        $b->tipoM = (strpos(Input::get('chiave')[$i], 'EUO_') !== false ? Input::get('type')[$i] : "");
        $b->valueM = Input::get('value')[$i];

        $t = explode("_", Input::get('chiave')[$i]);

        switch($t[0]){
          case "EUO":
            $d = Disponibili::where('typeD', '=', $t[0]."_".$t[1])
              ->get();
            $j = json_decode($d[0]->fileD, true);
            $b->quotaM = $j[$t[2]][Input::get('type')[$i]][Input::get('value')[$i]];
            break;
          case "SN":
            $d = Disponibili::where('typeD', '=', $t[0]."_".$t[1])
              ->get();
            $j = json_decode($d[0]->fileD, true);
            $b->quotaM = $j[$t[2]][Input::get('value')[$i]];
            break;
          case "MT":
            $d = Disponibili::where('typeD', '=', $t[0]."_".$t[1])
              ->get();
            $j = json_decode($d[0]->fileD, true);
            $b->quotaM = $j[explode("-", Input::get('value')[$i])[0]]['quota'];
            break;
        }
        $b->save();
      }
      return redirect(route('home'));
    }else{

    }
  }*/

}
