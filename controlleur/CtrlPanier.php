<?php
require_once ('modele/panier.php');

class CtrlPanier
{
    private $twig;
    private $panier;

    public function __construct($twig,$connected,$id)
    {
        $this->twig = $twig;
        $this->panier = new panier($connected,$id);
    }

    public function afficherPanier()
    {
        var_dump($this->panier->getPanier());
        echo $this->twig->render("panier.html.twig",[ 
            'cart' => $this->panier->getPanier()
            ]);
    }
}