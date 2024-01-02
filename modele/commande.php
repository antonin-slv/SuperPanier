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



    public function getUserInfo($id) {
        $sql = "SELECT customer_id FROM orders WHERE id = ?";
        $userID = $this->executerRequete($sql, array($id))->fetch()['customer_id'];
        //on récupère les infos du client
        $sql = "SELECT * FROM customers WHERE id = ?";
        $user = $this->executerRequete($sql, array($userID))->fetch();
        return $user;
    }
    public function getAdress($id = null) {
        if ($id == null) $id = $this->id;
        $sql = "SELECT delivery_add_id as id FROM orders WHERE id = ?";
        $commande = $this->executerRequete($sql, array($id))->fetch();
        //on récupère les infos de l'adresse
        $sql = "SELECT adresse_id FROM delivery_addresses WHERE id = ?";
        $adresse = $this->executerRequete($sql, array($commande['id']))->fetch();
        if ($adresse == null) return null;
        $sql = "SELECT * FROM adresses WHERE id = ?";
        $adresse = $this->executerRequete($sql, array($adresse['adresse_id']))->fetch(); 

        return $adresse;
    }
    public function getCommandInfo($id) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $commande = $this->executerRequete($sql, array($id))->fetch();
        
        $commande['user'] = $this->getUserInfo($id);

        $commande['nb_produits'] = 0;

        //on récupère les infos des produits
        $this->id = $id;
        $products = $this->loadBDDProducts();//force actualisation par la BDD
        //on passe les id en clefs
        foreach ($products as $value) {
            $commande['nb_produits'] += $value;
        }

        return $commande;
    }

    public function validate($id = null) {
        if ($id == null) $id = $this->id;
        $sql = "UPDATE orders SET status = 10 WHERE id = ?";
        $this->executerRequete($sql, array($this->id));
    }
}