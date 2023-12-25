<?php
require("connect.php");


abstract class Modele
{

    private $connexion;

    function getBDD()
    {
        if ($this->connexion == NULL) {
            $dsn = "mysql:dbname=" . BASE . ";host=" . SERVER;
            try {
                $this->connexion = new PDO($dsn, USER, PASSWD);
            } catch (PDOException $e) {
                printf("Échec de la connexion : %s\n", $e->getMessage());
                $this->connexion = NULL;
            }
        }
        return $this->connexion;
    }
    protected function executerRequete($sql, $params = null)
    {
        $BDD = $this->getBDD();
        if ($params == null) {
            $resultat = $BDD->query($sql); // exécution directe
            $resultat = $resultat->fetchAll();
        } else {

            $resultat = $BDD->prepare($sql); // requête préparée
            $resultat->execute($params);
        }
        
        return $resultat;
    }
    /**
     * Fonction créant une Adresse dans la BDD à partir des données POST
     * @param il faut que le POST soit remplis
     * @return mixed 
     */

    protected function createAdresseFromPOST()
    {   

        $adresse = array();
        $adresse['numero_rue'] = $_POST['numero_rue'];
        $adresse['rue'] = $_POST['rue'];
        $adresse['ville'] = $_POST['ville'];
        $adresse['code_postal'] = $_POST['code_postal'];
        $adresse['info_supp'] = $_POST['info_supp'];

        $sql = "INSERT INTO adresses (numero, rue, ville, code_postal, info_supp, Pays) ";
        $sql .= "VALUES (:num, :rue, :ville, :codePostal, :infoSupp, :pays)";
        return $this->executerRequete($sql, array(
            'num' => $_POST['numero_rue'],
            'rue' => $_POST['rue'],
            'ville' => $_POST['ville'],
            'codePostal' => $_POST['code_postal'],
            'infoSupp' => $_POST['info_supp'],
            'pays' => 'France'
        ));
    }
    /**
     * Fonction créant un User dans la BDD à partir des données POST
     * @param $adresseId : l'id de l'adresse du user
     * @return mixed 
     */
    protected function createUserFromPOST($adresseId)
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
    
    protected function createLogin($userId,$pseudo)
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
/*
    function add_friend($data)
    {
        $sql = "INSERT INTO contacts(NOM,PRENOM,NAISSANCE,ADRESSE,VILLE) values (?,?,?,?,?)";
        $stmt = self::$connexion->prepare($sql);
        return $stmt->execute(array(
            $data['nom'],
            $data['prenom'], $data['naissance'], $data['adresse'], $data['ville']
        ));
    }*/


