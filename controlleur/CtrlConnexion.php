<?php
class CtrlConnexion {
    private $twig;

    public function __construct($twig) {
        $this->twig = $twig;
    }

    public function afficherConnexion() {
        echo $this->twig->render('connexion.html.twig');
    }   
}