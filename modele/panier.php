<?php
require_once('modele/modele.php');

class panier extends Modele {
    private $panier = array();
    public function __construct() {
        // $sql = "SELECT * FROM panier";
        // $this->panier = $this->executerRequete($sql);
    }
}