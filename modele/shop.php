<?php
require_once('modele/modele.php');
class shop extends Modele {

    private $produits = array();
    public function __construct($produits) {
        //en fonction de la page, on va chercher les produits correspondants
        switch ($produits) {
            case 'biscuits':
                $type = 2;
                break;
            case 'boissons':
                $type = 1;
                break;
            default:
                $type = 3;
                break;
        }
        $sql = "SELECT * FROM products WHERE cat_id = $type;";
        $this->produits = $this->executerRequete($sql);
    }
    
    public function getProduits() {
        return $this->produits;
    }
}