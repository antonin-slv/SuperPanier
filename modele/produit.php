<?php
require_once('modele/modele.php');

class produit extends Modele {

    private $produit = array();
    public function __construct($produit) {
        //en fonction de la page, on va chercher les produits correspondants
        $sql = "SELECT * FROM products WHERE id = $produit;";
        $this->produit = $this->executerRequete($sql);
    }
    
    public function getProduit() {
        return $this->produit;
    }
}