<?php
require_once('modele/modele.php');

class paiement extends Modele
{

    //Id du user
    private $user_id;

    //Adresse de livraison / facturation
    private $adresseFacturation;
    private $adresseLivraison;

    //Informations de paiement
    private $modePaiement;

    //panier
    private $panier;


    public function __construct($user_id = null) // pour l'initialisation on a besoin de l'id du user ( il est forcément connecté )
    {
        if ($user_id == null) return;

        $this->user_id = $user_id;
        $this->loadBddInfo();
        $this->panier = new panier($this->user_id);
    }

    public function loadBddInfo()
    {
        $sql = "SELECT * FROM adresses WHERE id = (SELECT Adresse_id FROM customers WHERE id = $this->user_id )";
        $this->adresseFacturation = $this->executerRequete($sql)->fetch();
        $sql = "SELECT * FROM adresses WHERE id = (SELECT delivery_add_id FROM orders WHERE customer_id = $this->user_id AND status = 0)";
        $this->adresseLivraison = $this->executerRequete($sql)->fetch();
        $sql = "SELECT payment_type FROM orders WHERE customer_id = $this->user_id AND status = 0";
        $this->modePaiement = $this->executerRequete($sql)->fetch();

    }

    public function saveAdresseFacturationInBdd($Adresse, $user_id)
    {
        $sql = "INSERT INTO adresses ( numero, rue, ville, code_postal, Pays, info_supp ) VALUES ( ?, ?, ?, ?, ?, ? )";
        $this->executerRequete($sql, array(
            $Adresse['numero'],
            $Adresse['rue'],
            $Adresse['ville'],
            $Adresse['code_postal'],
            $Adresse['Pays'],
            $Adresse['info_supp'],
        ));
        $sql = "UPDATE customers SET adresse_id = (SELECT MAX(id) FROM adresses) WHERE id = $user_id";
        $this->executerRequete($sql);
    }

    public function saveAdresseLivraisonInBdd($Adresse, $user_id)
    {
        $sql = "INSERT INTO adresses ( numero, rue, ville, code_postal, Pays, info_supp ) VALUES ( ?, ?, ?, ?, ?, ? )";
        $this->executerRequete($sql, array(
            $Adresse['numero'],
            $Adresse['rue'],
            $Adresse['ville'],
            $Adresse['code_postal'],
            $Adresse['Pays'],
            $Adresse['info_supp'],
        ));
        $sql = "UPDATE orders SET delivery_add_id = (SELECT MAX(id) FROM adresses) WHERE customer_id = $user_id AND status = 0";
        $this->executerRequete($sql);
    }

    public function saveModePaiementInBdd($modePaiement, $user_id)
    {
        $sql = "UPDATE orders SET payment_type = '$modePaiement' WHERE customer_id = $user_id AND status = 0";
        $this->executerRequete($sql);
    }

    public function getAdresseFacturation()
    {
        return $this->adresseFacturation;
    }

    public function getAdresseLivraison()
    {
        return $this->adresseLivraison;
    }

    public function getModePaiement()
    {
        return $this->modePaiement;
    }


    public function payerEtFacturer()
    {
        $sql = "UPDATE orders SET status = 1 WHERE customer_id = $this->user_id AND status = 0";
        $this->executerRequete($sql);
        $this->facture();
    }

    public function facture()
    {
                //TODO !!!
    }   
}
