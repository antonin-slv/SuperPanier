<?php
require_once ('modele/paiement.php');
require_once ('modele/panier.php');

class CtrlPaiement
{
    private $twig;
    private $paiement;
    private $panier;
        
    public function __construct($twig,$user_id)
    {
        $this->twig = $twig;
        $this->paiement = new paiement($user_id);
        $this->panier = new panier(true);
    }

    public function afficherPaiement()
    {
        echo $this->twig->render("paiement.html.twig",
        [
            'adresseFacturation'=> $this->paiement->getAdresseFacturation(),
            'adresseLivraison'=> $this->paiement->getAdresseLivraison(),
            'modePaiement'=> $this->paiement->getModePaiement()
        ]);
    }
}
