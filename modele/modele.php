<?php
require("connect.php");

/** Classe de gestion des contacts servant de modèle
 *   à notre application avec des méthodes de type CRUD
 */
class CoBdd
{
    /** Objet contenant la connexion pdo à la BD */

    private static $connexion;

    /** Constructeur établissant la connexion */
    function __construct()
    {
        $dsn = "mysql:dbname=" . BASE . ";host=" . SERVER;
        try {
            self::$connexion = new PDO($dsn, USER, PASSWD);
        } catch (PDOException $e) {
            printf("Échec de la connexion : %s\n", $e->getMessage());
            $this->connexion = NULL;
        }
    }

    /** Récupére la liste des contacts sous forme d'un tableau */
    function get_all_friends()
    {
        $sql = "SELECT * from contacts";
        $data = self::$connexion->query($sql);
        return $data;
    }

    /** Ajoute un contact à la table contacts */
    function add_friend($data)
    {
        $sql = "INSERT INTO contacts(NOM,PRENOM,NAISSANCE,ADRESSE,VILLE) values (?,?,?,?,?)";
        $stmt = self::$connexion->prepare($sql);
        return $stmt->execute(array(
            $data['nom'],
            $data['prenom'], $data['naissance'], $data['adresse'], $data['ville']
        ));
    }

    /** Récupére un contact à partir de son ID */
    function get_friend_by_id($id)
    {
        $sql = "SELECT * from contacts where ID=:id";
        $stmt = self::$connexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /** Efface un contact à partir de son ID */
    function delete_friend_by_id($id)
    {
        $sql = "Delete from contacts where ID=:id";
        $stmt = self::$connexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    /** Met jour d'une personne avec sa date de naissance son adresse et sa ville */
    function patch($id, $naissance, $adresse, $ville)
    {
        $sql = "UPDATE `contacts` SET `NAISSANCE` = :naissance, `ADRESSE` = :adresse, `VILLE` = :ville
	 	WHERE `contacts`.`ID` = :id";
        $stmt = self::$connexion->prepare($sql);
        $stmt->bindParam(':naissance', $naissance);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /** Met à jour d'une personne avec son nom, son prénom,
     *  sa date de naissance et sa ville */
    function update($id, $nom, $prenom, $naissance, $ville)
    {
        $sql = "UPDATE `contacts `SET `NOM` = :nom,
                SET `PRENOM` = :prenom,
                SET `NAISSANCE` = :naissance,
                SET `VILLE` = :ville
	 	        WHERE `contacts`.`ID` = :id";
        $stmt = self::$connexion->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':naissance', $naissance);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
