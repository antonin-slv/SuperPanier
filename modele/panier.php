<?php
require_once('modele/modele.php');

class panier extends Modele {
    private $contenu = null;
    public $id = null;
    private $connected = false;

    private $total_price = 0;

<<<<<<< HEAD
    public function __construct($connected) {
        if (gettype($connected) == "array") {

            //dans ce cas c'est le tableau de session
            if (isset($connected['id'])) $this->id = $connected['id'];

            $this->contenu = $connected;
            unset($this->contenu['id']);//on ne garde que les produits

            $this->connected = $_SESSION['Connected'];
        }
        else $this->connected = $connected;
    }


    public function getProducts() {
        if ($this->id == null) return array();
        elseif (gettype($this->contenu) == "array") return $this->contenu;
        else $this->loadBDDProducts();
        return $this->contenu;
    }

    //met l'id du panier dans $this->id et dans $_SESSION['Panier']['id']
=======
        //Récupère un ID de panier ou le créé si il n'existe pas
        $this->setPanierID($id);
    }

>>>>>>> f6fdc5af4d66c22a9399f875e665c7a8749a23a8
    public function setPanierID($id) {
        
        if ($this->connected) {
            if($this->setIDfromUSER($id)) return;
        }
        else if ($this->setIDfromSESSION($id)) return;
        $this->createPanier($id);
    }

    public function setIDfromUSER($user_id) {
        $sql = "SELECT * FROM orders WHERE status = 0 AND customer_id = ? ORDER BY date DESC";
        $this->id = $this->executerRequete($sql, array($user_id))->fetch();
        if ($this->id) {//si il y a un résultat
            if (key_exists('id',$this->id)) $this->id = $this->id['id'];//1 linge, direct ID
            else $this->id = $this->id[0]['id'];//plsrs lignes, la première (+ récente)
            $_SESSION['Panier']['id'] = $this->id;
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
            $_SESSION['Panier']['id'] = $this->id;
            return true;
        }
        return false;
    }

    public function loadBDDProducts() {
<<<<<<< HEAD
        $sql = "SELECT product_id, quantity FROM orderitems WHERE order_id = ?";
=======
        //$sql = "SELECT * FROM orderitems WHERE order_id = ?";
        $sql = "SELECT o.id, o.order_id, o.product_id, o.quantity, p.cat_id, p.name, p.image, p.price FROM orderitems o JOIN products p ON o.product_id = p.id WHERE order_id = ?";
>>>>>>> f6fdc5af4d66c22a9399f875e665c7a8749a23a8
        $this->contenu = $this->executerRequete($sql, array($this->id))->fetchAll();
        // on utilise les id des produits comme clé
        $temp = array();
        foreach ($this->contenu as $key => $value) {
            $temp[$value['product_id']] = $value['quantity'];
            unset($this->contenu[$key]);
        }
        $this->contenu = $temp;
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
        $_SESSION['Panier']['id'] = $this->id;
    }

    public function addProduct($id, $qte) {

        // les stocks ont ils étés vérifiés avant ? ... normalement oui
        $sql = "SELECT * FROM orderitems WHERE order_id = ? AND product_id = ?";
        $rslt = $this->executerRequete($sql, array($this->id, $id))->fetch();
        if ($rslt) {//si il y a un résultat, alors le produit est déjà dans le panier
            var_dump($rslt);
            $sql = "UPDATE orderitems SET quantity = ? WHERE order_id = ? AND product_id = ?";
            $this->executerRequete($sql, array( $qte + $rslt["quantity"], $this->id, $id));
        }
        else {//sinon, on l'ajoute
            $sql = "INSERT INTO orderitems (order_id, product_id, quantity) VALUES (?, ?, ?)";
            $this->executerRequete($sql, array($this->id, $id, $qte));
        }
        
        $_SESSION['Panier'][$id] = $qte;

        $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $this->executerRequete($sql, array($qte, $id));

        $this->updatePrice();//on met le prix total dans la BDD et dans l'objet
    }

<<<<<<< HEAD
    public function updatePrice() {
        //on met à jour le prix total
        $sql = "SELECT SUM(p.price*o.quantity) as total FROM products p JOIN orderitems o ON o.product_id = p.id WHERE o.order_id = ?";
        $rslt = $this->executerRequete($sql, array($this->id))->fetch();
        $sql = "UPDATE orders SET total = ? WHERE id = ?";
        $this->executerRequete($sql, array($rslt['total'], $this->id));
        $this->total_price = $rslt['total'];
    }

    public function fromGuestToUser($user_id) {
        //ajout de l'id user et de son adresse dans la BDD
        $sql = "UPDATE orders SET customer_id = ?, delivery_add_id = ?, registered = 1 WHERE id = ?";
        $this->executerRequete($sql, array($user_id, $this->getUserAdress($_SESSION['user_id']), $this->id));
        $this->connected = true;
    }

    public function getProductInfo($id) {
        $sql = "SELECT id, name,image,price,quantity as stock FROM products WHERE id = ?";
        $rslt = $this->executerRequete($sql, array($id));
        return $rslt->fetch();
=======
    public function getPanier() {
        $this->loadBDDProducts();
        return $this->contenu;
>>>>>>> f6fdc5af4d66c22a9399f875e665c7a8749a23a8
    }

    public function removeProduct($id) {
        $sql = "DELETE FROM orderitems WHERE order_id = ? AND product_id = ?";
        $this->executerRequete($sql, array($this->id, $id));
        unset($_SESSION['Panier'][$id]);
    }
}