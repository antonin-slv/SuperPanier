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
    }

    public function afficherPanier()
    {   
        //pas de panier
        if (!$this->panier) {
            echo $this->twig->render("panier.html.twig", array('products' => array()));
            return;
        }

        $product_info = array();
        // on donne les noms corrects à chaque produit
        foreach ($this->panier->loadBDDProducts() as $key => $value) {
            //on récupère les infos du produit
            $product_info[$key] = Array('quantity'=> $value) + $this->panier->getProductInfo($key);
        }
        $this->panier->updatePrice();
        echo $this->twig->render("panier.html.twig", array('products' => $product_info, 'total' => $this->panier->total_price, 'Connected' => $_SESSION['Connected']));
    }

    public function syncPanierHuns(){
        
        //Si l'utilisateur s'est connecté sans toucher au panier
        if (!isset($_SESSION['Panier'])) {
                    
            //on dit juste au panier si l'user est connecté
            $panier = new Panier($_SESSION['Connected']);
            //soit on récupère l'ancien panier de l'utilisateur, soit on lui en crée un nouveau
            $panier->setPanierID($_SESSION['user_id']);
            //on récupère les produits du panier (ou rien XD)
            $_SESSION['Panier'] = $panier->loadBDDProducts();
            $_SESSION['Panier']['id'] = $panier->id;
        }
        else //si l'utilisateur a touché au panier
        { 
            //on récupère le panier en cours
            $panier = new Panier($_SESSION['Panier']);

            if ($_SESSION['Connected'] ){ //si l'utilisateur c'est connecté

                $vraispanier = new Panier(true);
                $vraispanier->setPanierID($_SESSION['user_id']);
                $vraispanier->addCartToCart($panier->id);
                $panier->killCart();
                $_SESSION['Panier'] = $vraispanier->loadBDDProducts();
                $_SESSION['Panier']['id'] = $vraispanier->id;
            } 
            else { //si l'utilisateur vient de créer un compte
                $panier->fromGuestToUser($_SESSION['futur_user_id']);
                $panier->setPanierID($_SESSION['user_id'], true , $_SESSION['futur_user_id']);
                $_SESSION['Panier']['id'] = $panier->id;
            }
            
        }
    }
}