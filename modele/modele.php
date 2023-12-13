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
        if ($params == null) {
            $resultat = $this->getBdd()->query($sql); // exécution directe
        } else {
            $resultat = $this->getBdd()->prepare($sql); // requête préparée
            $resultat->execute($params);
        }
        return $resultat;
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


