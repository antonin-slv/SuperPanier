<?php

require_once 'modele/user.php';
class CtrlConnexion {
    private $twig;
    private $user;
    public $error="";

    public function __construct($twig) {
        $this->twig = $twig;
    }
    public function connexion() {
        $this->user = new user();
        if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {
            if ( $this->user->connectUser($_POST['pseudo'],$_POST['mdp'])) {
                $_SESSION['user_id'] = $this->user->getID();
                $_SESSION['Connected'] = true;
                return true;
            }
        }
        $this->error = "login";
        return false;
    }
    public function register() {
        if (isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) )
        {
            $this->user = new user();
            //on créé l'utilisateur dans la bdd
            $rslt = $this->user->registerUserPOST();
            if ($rslt !== true) {
                $this->error = $rslt;
                return false;
            }
        }
        $this->error = "register";
        return false;

    }

    public function afficherPage($page)
    {
        if ($page == 'register') {
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