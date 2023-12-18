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

    
    /*
    public function __construct($string) {
        parent::__construct();
        if ($string == "create") {
            $this->createUser();
            $this->connectUser();
        }
        elseif ($string == "connect") {
            $this->connectUser();
        }
        elseif ($string == "disconnect") {
            $this->disconnectUser();
        }
    }

    public function createUser() {
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

        
        //on insère dans la bdd
        $sql="INSERT INTO customer (forname, surname, email, add1, add2, add3, postcode, phone,registered) ";
        $sql.="VALUES (:prenom, :nom, :mail, :num, :rue, :ville, :codePostal, :telephone, :registered)";
        $req = $this->getBDD()->prepare($sql);

        $req->execute(array(
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'mail' => $this->mail,
            'num' => $this->adresse['numero_rue'],
            'rue' => $this->adresse['rue'],
            'ville' => $this->adresse['ville'],
            'codePostal' => $this->adresse['code_postal'],
            'telephone' => $this->numero_tel,
            'registered' => $this->registered
        ));

        //on récupère l'id du user avec la dernière insertion
        $this->ID = $this->getBDD()->lastInsertId();

        //on hash le mdp
        $this->mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

        //on insère le mdp hashé dans la bdd
        $sql="INSERT INTO login (Customer_id, password, username) ";
        $sql.="VALUES (:id, :mdp, :pseudo)";
        $req = $this->getBDD()->prepare($sql);
        $req->execute(array(
            'id' => $this->ID,
            'mdp' => $this->mdp,
            'pseudo' => $this->pseudo
        ));


    }
    */
}