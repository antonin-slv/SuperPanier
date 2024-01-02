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
        if (!isset($_SESSION['admin'])) $oups = true;
        else if($_SESSION['admin'] !== true) $oups = true;
        if (isset($oups)) {
            echo $this->twig->render('404.html.twig', array('page' => 'DU BIST NICHT EINE ADMINISTRATOR'));
            return;
        }
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

        $commande = new commande(intval($id));
        $info = $commande->getCommandInfo($id);//faire en 1ere requete (charge produits)

        // on donne les noms corrects à chaque produit
        foreach ($commande->getProducts() as $key => $value) {
            //on récupère les infos du produit
            $product_info[$key] = Array('quantity'=> $value) + $commande->getProductInfo($key);
        }
        //on récupère les infos de la commande

        $info['adresse'] = $commande->getAdress($id);
        $info['id'] = $id;
        //ce serra un truc qui ressemblera bcp au panier...
        //echo $this->twig->render('panier.html.twig', array('products' => $product_info, 'total' => $commande->total_price));
        echo $this->twig->render('panier.html.twig', array('products' => $product_info, 'info' => $info,'admin' => true));
    }   
}
