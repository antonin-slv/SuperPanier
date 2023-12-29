<?php
require_once ('modele/panier.php');

class CtrlPanier
{
    private $twig;
    private $panier;

    public function __construct($twig,$connected,$id)
    {
        $this->twig = $twig;
        
        if (isset($_SESSION['Panier'])) $this->panier = new panier($_SESSION['Panier']);
        else $this->panier = false;
        //var_dump($this->panier);
    }

    public function afficherPanier()
    {   //pas de panier
        if (!$this->panier) {
            echo $this->twig->render("panier.html.twig", array('products' => array()));
            return;
        }

        $product_info = array();
        // on donne les noms corrects à chaque produit
        foreach ($this->panier->getProducts() as $key => $value) {
            //on récupère les infos du produit
            $product_info[$key] = Array('quantity'=> $value) + $this->panier->getProductInfo($key);
        }
        echo $this->twig->render("panier.html.twig", array('products' => $product_info, 'total' => $this->panier->total_price));
    }
}