<?php
require("connect.php");


abstract class Modele {

    private $connexion = NULL;
    
    function getBDD()
    {
        if (self::$connexion == NULL) {
            $dsn = "mysql:dbname=" . BASE . ";host=" . SERVER;
            try {
                self::$connexion = new PDO($dsn, USER, PASSWD);
            } catch (PDOException $e) {
                printf("Échec de la connexion : %s\n", $e->getMessage());
                $this->connexion = NULL;
            }
        }
        return $this->connexion;
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

}
