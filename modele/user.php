<?php
include_once('modele/modele.php');
class user extends Modele {
    private $connected = false;
    private $ID;
    private $pseudo;
    private $adrID;
    private $registered;
    private $admin = false;

    
    
    public function __construct() {
    }
    public function isAdmin()
    {
        return $this->admin;
    }
    public function connectUser($pseudo,$mdp)
    {
        $sql="SELECT customer_id, password FROM logins WHERE username =\"". $pseudo ."\"";
        $rslt = $this->executerRequete($sql);
        $rslt = $rslt->fetch();
        if ($rslt == false) {
            $sql="SELECT id, password FROM admin WHERE username =\"". $pseudo ."\"";
            $rslt = $this->executerRequete($sql)->fetch();

            if ($rslt == false) return false;
            else $this->admin = true;
        }
        if (password_verify($mdp, $rslt['password'])) {
            if (isset($rslt['customer_id'])) $this->ID = $rslt['customer_id'];
            else $this->ID = $rslt['id'];
            $this->pseudo = $pseudo;
            $this->connected = true;
            return true;
        }
        return false;
    }

    public function getID()
    {
        return $this->ID;
    }
    /**
     * Fonction qui créé un utilisateur de A à Z à partir des données POST
     * @return mixed : une chaine donnant l'erreur, true si tout s'est bien passé
     */
    public function registerUserPOST() {
        //vérification du pseudo
        $sql="SELECT * FROM logins WHERE username =\"". $_POST['pseudo'] ."\"";
        $rslt = $this->executerRequete($sql);
        $rslt = $rslt->fetch();
        if ($rslt) {//si il y a un résultat, alors le pseudo est déjà pris
            
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

    /**
     * Fonction créant un User dans la BDD à partir des données POST
     * @param $adresseId : l'id de l'adresse du user
     * @return mixed 
     */
    private function createUserFromPOST($adresseId)
    {
        $sql = "INSERT INTO customers (forname, surname, email, phone, registered, adresse_id) ";
        $sql .= "VALUES (:prenom, :nom, :mail, :numeroTel, :registered, :adresseId)";
        return $this->executerRequete($sql, array(
            'prenom' => $_POST['prenom'],
            'nom' => $_POST['nom'],
            'mail' => $_POST['mail'],
            'numeroTel' => $_POST['numero_tel'],
            'registered' => '1',
            'adresseId' => $adresseId
        ));
    }
    
    private function createLogin($userId,$pseudo)
    {
        //on hash le mdp
        $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

        //on insère le mdp hashé dans la bdd
        $sql="INSERT INTO logins (customer_id, password, username) ";
        $sql.="VALUES (:id, :mdp, :pseudo)";
        $this->executerRequete($sql,
            array(
                'id' => $userId,
                'mdp' => $mdp,
                'pseudo' => $pseudo
            )
        );
    }
}