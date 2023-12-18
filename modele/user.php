<?php
include_once('modele/modele.php');
class user extends Modele {
    private $connected = false;
    private $ID;
    private $pseudo;
    private $prenom;
    private $nom;
    private $mail;
    private $adresse;
    private $numero_tel;
    private $registered;
    private $admin;

    
    
    public function __construct() {
    }

    public function createUser() {
        //vérification du pseudo
        $sql="SELECT * FROM logins WHERE username =\"". $_POST['pseudo'] ."\"";
        $resultat = $this->executerRequete($sql);
        if ($resultat) {
            return "Pseudo";
        }
        /*
        //init des variables
        $this->prenom = $_POST['prenom'];
        $this->nom = $_POST['nom'];
        $this->mail = $_POST['mail'];
        $this->numero_tel = $_POST['numero_tel'];
        $this->registered = true;
        $this->admin = false;
        $this->pseudo = $_POST['pseudo'];

        //on récupère les infos de l'adresse
        $this->adresse['numero_rue'] = $_POST['numero_rue'];
        $this->adresse['rue'] = $_POST['rue'];
        $this->adresse['ville'] = $_POST['ville'];
        $this->adresse['code_postal'] = $_POST['code_postal'];

        //on insère l'adresse dans la bdd
        $sql="INSERT INTO adresse (numero_rue, rue, ville, code_postal) ";
        $sql.="VALUES (:num, :rue, :ville, :codePostal)";
        $this->executerRequete($sql,
            array(
                'num' => $this->adresse['numero_rue'],
                'rue' => $this->adresse['rue'],
                'ville' => $this->adresse['ville'],
                'codePostal' => $this->adresse['code_postal']
            )
        );

        //on récupère l'id de l'adresse avec la dernière insertion
        $this->adresse['id'] = $this->getBDD()->lastInsertId();

        //on insère le user dans la bdd
        $sql="INSERT INTO customer (first_name, last_name, email, phone_number, registered, admin, address_id) ";
        $sql.="VALUES (:prenom, :nom, :mail, :numeroTel, :registered, :admin, :adresseId)";
        $this->executerRequete($sql,
            array(
                'prenom' => $this->prenom,
                'nom' => $this->nom,
                'mail' => $this->mail,
                'numeroTel' => $this->numero_tel,
                'registered' => $this->registered,
                'admin' => $this->admin,
                'adresseId' => $this->adresse['id']
            )
        );

        //on récupère l'id du user avec la dernière insertion
        $this->ID = $this->getBDD()->lastInsertId();

        //on hash le mdp
        $this->mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

        //on insère le mdp hashé dans la bdd
        $sql="INSERT INTO logins (Customer_id, password, username) ";
        $sql.="VALUES (:id, :mdp, :pseudo)";
        $this->executerRequete($sql,
            array(
                'id' => $this->ID,
                'mdp' => $this->mdp,
                'pseudo' => $this->pseudo
            )
        );
        */
    }
}