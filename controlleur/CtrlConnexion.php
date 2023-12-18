<?php
class CtrlConnexion {
    private $twig;
    private $user;

    public function __construct($twig) {
        $this->twig = $twig;
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            if ($action == 'connexion') {
                $this->connexion();
                header("Location:index.php?page=accueil");
            }
            elseif ($action == 'register') {
                $this->register();
            }
            elseif ($action == 'deconnexion') {
                $this->deconnexion();   
                header("Location:index.php?page=accueil");   
            }     
        }
    }

    public function connexion() {

    }
    public function register() {
        if (isset($_POST['mail']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) )
        {

            $this->user = new user("create");
            $this->user->register();
        

        }
    }
    public function deconnexion() {

    }
    public function afficherConnexion() {
        echo $this->twig->render('connexion.html.twig');
    }   

    public function afficherRegister() {
        echo $this->twig->render('register.html.twig');
    }
}