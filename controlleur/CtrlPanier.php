<?php
require_once ('modele/panier.php');

class CtrlPanier
{
    private $twig;
    private $panier;

    public function __construct($twig)
    {
        $this->twig = $twig;
        $this->panier = new panier();
    }

    public function afficherPanier()
    {
        echo $this->twig->render("panier.html.twig",[ 
            'cart' => $this->panier->getPanier()
            ]);
    }
}