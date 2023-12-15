<?php
require_once 'modele/Shop.php';

class CtrlShop
{
    private $twig;
    private $page;
    private $shop;

    public function __construct($twig,$page)
    {
        $this->twig = $twig;
        /* charger+compiler le template, exécuter, envoyer le résultat au navigateur */
        $this->page = $page;
        $this->shop = new Shop($page);
    }

    public function afficherShop()
    {
        echo $this->twig->render("shop.html.twig",[
            'shop'=> $this->shop->getProduits(), 
            'page'=> $this->page
            ]);
    }
}