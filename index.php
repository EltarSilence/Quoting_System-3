<?php
require_once 'autoloader.php';

require_once "classes/View.php";

ZRoute::get("/", function (){
  View::getView("home", "");
});
ZRoute::get("/home", function (){
  View::getView("home", "");
}, "homepage");


/*           CHIAMATE AJAX            */
ZRoute::post("/show_my_profile", function (){
  //Qui ci va lo script che deve essere esguito quando si fa una chiamata AJAX per mostrare il mio profilo
}, "my_profile");

ZRoute::listen();

?>
