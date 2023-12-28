<?php

require_once 'modele/user.php';
class CtrlConnexion {
    private $twig;
    private $user;

    private $page;

    private $error="";

    public function __construct($twig) {
        $this->twig = $twig;
        $this->page= $_GET['page'];

        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action == 'connexion') {
                if ($this->connexion()) {
                    //si l'utilisateur est connecté, on le redirige vers la page d'accueil
                    header("Location:index.php?page=accueil");
                }
                else {
                    //sinon, on l'envois sur la page de connexion avec le message d'erreur
                    $this->error = "login";
                    $this->page = 'connexion';
                }
            }
            elseif ($action == 'register') {
                $rslt = $this->register();
                if($rslt === true){
                    header("Location:index.php?page=connexion");
                    //une fois l'utilisateur créé, on le redirige vers la page de connexion
                }
                else{
                    //sinon, on l'envois sur la page register avec le message d'erreur
                    $this->error = $rslt;
                    $this->page = 'register';
                }
            
            }
            elseif ($action == 'deconnexion') {
                $this->deconnexion();
                header("Location:index.php?page=accueil");   
                //une fois l'utilisateur déconnecté, on le redirige vers la page d'accueil
            }     
        }
    }

    public function connexion() {
        $this->user = new user();
        if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {
            return $this->user->connectUser($_POST['pseudo'],$_POST['mdp']);
        }
        return false;
    }
    public function register() {
        if (isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) )
        {
            $this->user = new user();
            //on créé l'utilisateur dans la bdd
            return $this->user->registerUserPOST();
            
        }
        return false;
    }
    public function deconnexion() {
        $this->user = new user();
        $this->disconnectUser();

    }

    public function afficherPage() {

        if ($this->page == 'register') {
            //La page affichée est gérée par le controlleur (pour gérer plus facilement les erreurs)
            echo $this->twig->render(
                'register.html.twig',
                array(  'error' => $this->error,
                        'post' => $_POST
                    )
            );
        }
        //sinon on affiche la page de connexion
        else {
            
            echo $this->twig->render(
                'connexion.html.twig',
                array(  'error' => $this->error,
                        'post' => $_POST
                    )
            );
    
        }

    }
}