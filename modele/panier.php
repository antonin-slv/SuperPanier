<?php
require_once('modele/modele.php');

class panier extends Modele {
    private $contenu = array();
    private $id = null;
    private $connected = false;

    public function __construct($connected,$id) {
        $this->connected = $connected;

        //Récupère un ID de panier ou le créé si il n'existe pas
        $this->setPanierID($id);
    }

    public function setPanierID($id) {
        
        if ($this->connected) {
            if($this->setIDfromUSER($id)) return;
        }
        else if ($this->setIDfromSESSION($id)) return;
        $this->createPanier($id);
        echo "<p style="."color:red;"."> END SET ID</p>";
        var_dump($this);
    }

    public function setIDfromUSER($user_id) {
        $sql = "SELECT * FROM orders WHERE status = 0 AND customer_id = ? ORDER BY date DESC";
        $this->id = $this->executerRequete($sql, array($user_id))->fetch();
        if ($this->id) {//si il y a un résultat
            if (key_exists('id',$this->id)) $this->id = $this->id['id'];//1 linge, direct ID
            else $this->id = $this->id[0]['id'];//plsrs lignes, la première (+ récente)
            return true;
        }
        return false; //si il n'y a pas de panier en cours pour cet utilisateur
    }

    public function setIDfromSESSION($session_id) {
        $sql = "SELECT * FROM orders WHERE status = 0 AND session = ? ORDER BY date DESC";
        $this->id = $this->executerRequete($sql, array($session_id))->fetch();
        if ($this->id) {//si il y a une résultat
            if (key_exists('id',$this->id)) $this->id = $this->id['id'];// si il y a une seule ligne, on prend l'id directement
            else $this->id = $this->id[0]['id'];// si il y a plusieurs lignes, on prend la première (+ récente)
            return true;
        }
        return false;
    }

    public function loadBDDProducts() {
        //$sql = "SELECT * FROM orderitems WHERE order_id = ?";
        $sql = "SELECT o.id, o.order_id, o.product_id, o.quantity, p.cat_id, p.name, p.image, p.price FROM orderitems o JOIN products p ON o.product_id = p.id WHERE order_id = ?";
        $this->contenu = $this->executerRequete($sql, array($this->id))->fetchAll();
    }

    public function createPanier($id) {
        if ($this->connected) {
            $customer_id = $id;
            $session = session_id();
            $registered = 1;
            $sql = "SELECT adresse_id FROM customers WHERE id = ?";
            $id_address = $this->executerRequete($sql, array($id))->fetch()['adresse_id'];
        }
        else {
            $customer_id = -1;
            $session = $id;
            $registered = 0;
            $id_address = null;
        }
        $date = date("Y-m-d");
        
        $sql = "INSERT INTO orders (customer_id, session, date, status, registered,delivery_add_id) VALUES (?, ?, ?, 0, ?,?)";
        $this->executerRequete($sql, array($customer_id, $session, $date, $registered,$id_address));
        $this->id = $this->getBDD()->lastInsertId();
    }

    public function addProduct($id, $qte) {


        // les stocks ont ils étés vérifiés avant ? ... normalement oui
        $sql = "SELECT * FROM orderitems WHERE order_id = ? AND product_id = ?";
        $rslt = $this->executerRequete($sql, array($this->id, $id))->fetch();
        if ($rslt) {//si il y a un résultat, alors le produit est déjà dans le panier
            $sql = "UPDATE orderitems SET quantity = ? WHERE order_id = ? AND product_id = ?";
            $this->executerRequete($sql, array( $qte, $this->id, $id));
        }
        else {//sinon, on l'ajoute
            $sql = "INSERT INTO orderitems (order_id, product_id, quantity) VALUES (?, ?, ?)";
            $this->executerRequete($sql, array($this->id, $id, $qte));
        }
        
        $_SESSION['Panier'][$id] = $qte;

        $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $this->executerRequete($sql, array($qte, $id));
    }

    public function getPanier() {
        $this->loadBDDProducts();
        return $this->contenu;
    }

    public function removeProduct($id) {
        $sql = "DELETE FROM orderitems WHERE order_id = ? AND product_id = ?";
        $this->executerRequete($sql, array($this->id, $id));
        unset($_SESSION['Panier'][$id]);
    }
}