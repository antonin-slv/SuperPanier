<?php
require_once 'panier.php';

class commande extends panier {

    public function __construct($id) {
        parent::__construct($id);
    }


    /*
    la fonction valueToKey permet de passer un tableau de la forme array(array('id' => 1, 'nom' => 'toto'),array('id' => 2, 'nom' => 'titi'))
    en array(1 => array( 'nom' => 'toto'), 2 => array('id' => 2, 'nom' => 'titi'))
    */
    public function getAll() {
        $sql = "SELECT id FROM orders ORDER BY date DESC";
        $commandes = $this->executerRequete($sql)->fetchAll();
        return $this->valueToKey($commandes);
    }
    public function getAllToValidate() {
        $sql = "SELECT id FROM orders WHERE status = 2 ORDER BY date DESC";
        $commandes = $this->executerRequete($sql)->fetchAll();
        return $this->valueToKey($commandes);
    }
    public function getAllSent() {
        $sql = "SELECT id FROM orders WHERE status = 10 ORDER BY date DESC";
        $commandes = $this->executerRequete($sql)->fetchAll();
        return $this->valueToKey($commandes);
    }
    public function getAllUnfinished() {
        $sql = "SELECT id FROM orders WHERE status = 0 OR status = 1 ORDER BY date DESC";
        $commandes = $this->executerRequete($sql)->fetchAll();
        return $this->valueToKey($commandes);
    }


    public function getCommandInfo($id) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $commande = $this->executerRequete($sql, array($id))->fetch();
        //on récupère les infos du client
        $sql = "SELECT * FROM customers WHERE id = ?";
        $user = $this->executerRequete($sql, array($commande['customer_id']))->fetch();
        if ($user) {
            $commande['user'] = $user;
        }
        else {
            $commande['user']['forename'] = "Utilisateur";
            $commande['user']['surname'] = "Anonyme";
        }

        $commande['nb_produits'] = 0;
        //on récupère les infos des produits
        $sql = "SELECT product_id,quantity FROM orderitems WHERE order_id = ?";
        $products = $this->executerRequete($sql, array($id))->fetchAll();
        //on passe les id en clefs
        foreach ($products as $value) {
            $commande['produits'][$value['product_id']] = $value['quantity'];
            $commande['nb_produits'] += $value['quantity'];
        }
        return $commande;
    }
}