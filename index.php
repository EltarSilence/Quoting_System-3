<?php
require_once 'autoloader.php';

require_once "classes/View.php";
require_once "classes/Controller.php";
require_once "classes/DB.php";

/*  VISTE */
ZRoute::get("/", function (){
  Controller::checkAndPay();
  $weekWin = Controller::getWeekWin();
  $mouthWin = Controller::getMouthWin();
  View::getView("home", "", ['weekWin' => $weekWin, 'mouthWin' => $mouthWin]);
});

ZRoute::get("/home", function (){
  Controller::checkAndPay();
  $weekWin = Controller::getWeekWin();
  $mouthWin = Controller::getMouthWin();
  View::getView("home", "", ['weekWin' => $weekWin, 'mouthWin' => $mouthWin]);
}, "homepage");

ZRoute::get("/scommetti", function (){
  Controller::checkAndPay();
  View::getView("scommetti", "");
}, "scommetti");

ZRoute::get("/login", function ($data){
  Controller::checkAndPay();
  if(ZAuth::user() == false){
    View::getView("login", "", ['er' => $data]);
  }else{
    header("Location: home");
  }
}, "login");

ZRoute::get("/logout", function (){
  Controller::checkAndPay();
  Controller::logout();
}, "logout");

ZRoute::get("/register", function (){
  Controller::checkAndPay();
  if (ZAuth::user() == false) {
    View::getView("register", "");
  } else {
    header("Location: home");
  }
}, "register");

ZRoute::get("/my-bet", function (){
  Controller::checkAndPay();
  $s = Controller::myBet();
  View::getView("my-bet", "", ['scommesse' => $s]);
}, "my-bet");

/*  FUNZIONI  */
ZRoute::post("/getDisponibili", function (){
  Controller::checkAndPay();
  echo Controller::getDisponibili();
});

ZRoute::post("/getScommessa", function ($data){
  echo Controller::getScommessa($data);
});

ZRoute::post("/addScommessa", function ($data){
  Controller::addScommessa($data);
});

ZRoute::post("/login", function ($data){
	Controller::login($data);
});

ZRoute::post("/register", function ($data){
  Controller::register($data);
});

ZRoute::listen();
