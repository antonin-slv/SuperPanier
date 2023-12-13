<?php
//-- ------ Partie 1 : Front Loader ---------- -->

require_once 'controlleur/Routeur.php';

$routeur = new Routeur();
$routeur->routerRequete();