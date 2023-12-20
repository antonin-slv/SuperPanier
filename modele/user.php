<?php
include_once('modele/modele.php');
class user extends Modele {
    private $connected = false;
    private $ID;
    private $pseudo;
    private $adrID;
    private $registered;
    private $admin;

    
    
    public function __construct() {
    }


    /**
     * Fonction qui créé un utilisateur de A à Z à partir des données POST
     * @return mixed : une chaine donnant l'erreur, true si tout s'est bien passé
     */
    public function registerUserPOST() {
        //vérification du pseudo
        $sql="SELECT * FROM logins WHERE username =\"". $_POST['pseudo'] ."\"";
        if ($this->executerRequete($sql)) {//si il y a un résultat, alors le pseudo est déjà pris
            return "Pseudo";
        }
        //vérification de l'adresse
        if (!(isset($_POST['numero_rue']) && isset($_POST['rue']) && isset($_POST['ville']) && isset($_POST['code_postal']) && isset($_POST['info_supp']))) {
            return "Adresse";
        }
        //vérfication info user 
        if (!(isset($_POST['mail']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) )) {
            return "InfoUser";
        }

        $this->createAdresseFromPOST();//création adresse
        $this->adrID = $this->getBDD()->lastInsertId();

        $this->createUserFromPOST($this->adrID);//on insère le user dans la bdd

        $this->ID = $this->getBDD()->lastInsertId(); //on récupère l'id du user avec la dernière insertion
        $this->pseudo = $_POST['pseudo'];//on récupère le pseudo

        $this->createLogin($this->ID,$this->pseudo);//on créé son login 
        
        return true;
    }
}