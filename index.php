<?php


require_once 'controlleur/Routeur.php';

$routeur = new Routeur();
$routeur->initSession();
$routeur->routerRequete(); //c'est le routeur qui met en relation les controlleurs

echo "<p>POST : ";
var_dump($_POST);
echo "</p><p>SESSION : ";
var_dump($_SESSION);