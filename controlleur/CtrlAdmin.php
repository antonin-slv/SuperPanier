<?php

require_once 'modele/modele.php';
require_once 'modele/user.php';
require_once 'modele/commande.php';
require_once 'modele/produit.php';

class ctrlAdmin {
    public $twig;
    public $user;


    public function __construct($twig) {
        $this->twig = $twig;
    }

    public function afficherListeCommandes($status = -1) {
        /*
        //revérifier que l'utilisateur est bien admin
        if (!isset($_SESSION['admin'])) return;
        if($_SESSION['admin'] !== true) return;
        */
        $commande = new commande(null);
        //on récupère les commandes en fonction du status que l'on cherche
        switch ($status) {
            case 2:
                $commandes = $commande->getAllToValidate();
                break;
            case 10:
                $commandes = $commande->getAllSent();
                break;
            case -1 :
                $commandes = $commande->getAll();
                break;
            default:
                $commandes = $commande->getAllUnfinished();
                break;
        }
        //on récupère les infos générales de chaques commandes
        foreach ($commandes as $key => $value) {
            $commandes[$key] = $commande->getCommandInfo($value['id']);
        }

        echo $this->twig->render('admin.html.twig', array('commandes' => $commandes));
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
