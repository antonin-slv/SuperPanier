<?php

require_once 'modele/user.php';
class CtrlConnexion {
    private $twig;
    private $user;

    private $pseudo;

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
        $this->user = new user();
        $this->connectUser();
    }
    public function register() {
        if (isset($_POST['mail']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) )
        {
            $this->user = new user();
            if($this->user->createUser() == "Pseudo"){
                $this->user = "Pseudo";
                return;
            }
            /*
            $this->user->connectUser(); 
            $this->user->register();
            */
        }
    }
    public function deconnexion() {
        $this->user = new user();
        $this->disconnectUser();

    }

    public function afficherPage($page) {
        if ($page == 'register' || $this->user == "Pseudo") {
            //si user==pseudo, alors il n'a pas été créé car le pseudo existe déjà
            echo $this->twig->render(
                'register.html.twig',
                array('user' => $this->user,
                        'post' => $_POST
            ));
        }
        echo $this->twig->render($page . '.html.twig');
    }
}