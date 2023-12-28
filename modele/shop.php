<?php
require_once('modele/modele.php');
class shop extends Modele {

    private $type;
    private $produits = array();
    public function __construct($produits) {
        //en fonction de la page, on va chercher les produits correspondants
        switch ($produits) {
            case 'biscuits':
                $this->type = 2;
                break;
            case 'boissons':
                $this->type = 1;
                break;
            case 'fruits_sec':
                $this->type = 3;
                break;
            default:
                $this->type = 0;
                break;
        }
        $sql = "SELECT * FROM products WHERE cat_id = $this->type;";
        $rslt = $this->executerRequete($sql);
        $this->produits = $rslt->fetchAll();
    }

    public function ajouterProduit($id,$qte) {
        $sql = ""; // "INSERT INTO orders (id, qte) VALUES ($id, $qte);"               TODO avec les infos de l'utilisateur (Mais où ?)
        $this->executerRequete($sql);
    }

    public function MAJshop() {
        $sql = "SELECT * FROM products WHERE cat_id = $this->type;";
        $rslt = $this->executerRequete($sql);
        $this->produits = $rslt->fetchAll();
    }
    
    public function getProduits() {
        return $this->produits;
    }
}