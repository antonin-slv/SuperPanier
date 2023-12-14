<?php

require_once 'modele/panier.php';

class CtrlPanier
{
    private $twig;
    private $panier;

    public function __construct($twig)
    {
        $this->twig = $twig;
        /* charger+compiler le template, exécuter, envoyer le résultat au navigateur */
        $this->panier = new panier();
    }

    public function afficherPanier()
    {
        //$panier = $this->panier->getPanier();
        //echo $this->twig->render("panier.html.twig", $panier->fetchAll());
    }
}