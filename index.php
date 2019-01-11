<?php
require_once 'autoloader.php';

require_once "classes/View.php";
require_once "classes/Controller.php";
require_once "classes/DB.php";

/*  VISTE */
ZRoute::get("/", function (){
  $weekWin = Controller::getWeekWin();
  $mouthWin = Controller::getMouthWin();
  View::getView("home", "", ['weekWin' => $weekWin, 'mouthWin' => $mouthWin]);
});

ZRoute::get("/home", function (){
  $weekWin = Controller::getWeekWin();
  $mouthWin = Controller::getMouthWin();
  View::getView("home", "", ['weekWin' => $weekWin, 'mouthWin' => $mouthWin]);
}, "homepage");

ZRoute::get("/scommetti", function (){
  View::getView("scommetti", "");
}, "scommetti");

ZRoute::get("/login", function ($data){
  if(ZAuth::user() == false){
    View::getView("login", "", ['er' => $data]);
  }else{
    header("Location: home");
  }
}, "login");

ZRoute::get("/logout", function (){
  Controller::logout();
}, "logout");

ZRoute::get("/register", function (){
  if (ZAuth::user() == false) {
    View::getView("register", "");
  } else {
    header("Location: home");
  }
}, "register");

ZRoute::get("/my-bet", function (){
  $s = Controller::myBet();
  View::getView("my-bet", "", ['scommesse' => $s]);
}, "my-bet");

/*  FUNZIONI  */
ZRoute::post("/addScommessa", function (){

});

ZRoute::post("/login", function ($data){
	Controller::login($data);
});

ZRoute::post("/register", function ($data){
  Controller::register($data);
});

ZRoute::listen();
