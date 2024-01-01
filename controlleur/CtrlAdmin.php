<?php

require_once 'modele/modele.php';
require_once 'modele/user.php';
require_once 'modele/panier.php';
require_once 'modele/produit.php';

class ctrlAdmin {
    public $twig;
    public $user;


    public function __construct($twig) {
        $this->twig = $twig;
    }

    public function afficherListeCommandes() {

        echo $this->twig->render('listeCommandes.html.twig', array('commandes' => $commandes));
    }

    public function afficherCommande($id) {
        $commande = new Panier($id);
        $product_info = array();
        // on donne les noms corrects à chaque produit
        foreach ($commande->getProducts() as $key => $value) {
            //on récupère les infos du produit
            $product_info[$key] = Array('quantity'=> $value) + $this->panier->getProductInfo($key);
        }
        //ce serra un truc qui ressemblera bcp au panier...
        echo $this->twig->render('commande.html.twig', array('products' => $product_info, 'total' => $commande->total_price));
    }   
}
