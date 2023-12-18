<?php


require_once 'controlleur/Routeur.php';

$routeur = new Routeur();
$routeur->initSession();
$routeur->routerRequete(); //c'est le routeur qui met en relation les controlleurs
var_dump($_POST);