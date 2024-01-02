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
        } else {

            $resultat = $BDD->prepare($sql); // requête préparée
            $resultat->execute($params);
        }
        
        return $resultat;
    }
    /**
     * Fonction créant une Adresse dans la BDD à partir des données POST
     * @param none faut que le POST soit remplis
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
    protected function getUserAdress($user_id)
    {
        $sql = "SELECT adresse_id FROM customers WHERE id = ?";
        $rslt = $this->executerRequete($sql, array($user_id))->fetch();
        return $rslt['adresse_id'];
    }

    public function valueToKey($tab,$column = "id")
    {
        $result = array();
        foreach ($tab as $value) {
            $result[$value[$column]] = $value;
        }
        return $result;

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


