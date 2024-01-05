<?php
require_once('modele/modele.php');

class produit extends Modele {

    private $produit = array();

    public function __construct($produit= null) {
        if ($produit == null) return;
        //en fonction de la page, on va chercher les produits correspondants
        $sql = "SELECT * FROM products WHERE id = $produit;";
        $this->produit = $this->executerRequete($sql)->fetchAll();
    }
    
    public function getProduit() {
        return $this->produit;
    }

    public function addToStock($id, $quantity) {
        $sql = "UPDATE products SET quantity = quantity + $quantity WHERE id = $id";
        $this->executerRequete($sql);
    }
}