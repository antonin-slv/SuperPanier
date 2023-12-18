<?php
require_once ('modele/produit.php');

class CtrlProduit
{
    private $twig;
    private $produit;

    public function __construct($twig,$produit_id)
    {
        $this->twig = $twig;
        /* charger+compiler le template, exécuter, envoyer le résultat au navigateur */
        $this->produit = new produit($produit_id);
    }

    public function afficherProduit()
    {
        echo $this->twig->render("produit.html.twig",[
            'produit'=> $this->produit->getProduit()[0],
            ]);
    }
}