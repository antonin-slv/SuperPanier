<?php
require_once 'vendor/autoload.php'; //pour TWIG
include_once('CtrlPanier.php');
include_once('CtrlShop.php');
include_once('CtrlProduit.php');
class Routeur
{
    private $twig;

    public function __construct()
    {   //----------- On initialise TWIG -----------
        /* inclure l'autoloader */
        /* templates chargés à partir du système de fichiers (répertoire vue) */
        $loader = new Twig\Loader\FilesystemLoader('vue');
        /* options : prod = cache dans le répertoire cache, dev = pas de cache */
        $options_prod = array('cache' => 'cache', 'autoescape' => true);
        $options_dev = array('cache' => false, 'autoescape' => true);
        /* stocker la configuration */
        $this->twig = new Twig\Environment($loader);
        /* charger+compiler le template, exécuter, envoyer le résultat au navigateur */
    }
    public function routerRequete()
    {
        //en fonction des inputs, on charge la page correspondante
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if (!file_exists("vue/$page.html.twig")) {
                //on sort la page 404
                echo $this->twig->render('404.html.twig', array('page' => $page));
            }
            elseif ($page == 'shop') {
                if(isset($_GET['product'])){
                    $ctrlShop = new CtrlShop($this->twig,$_GET['product']);
                    $ctrlShop->afficherShop();
                }else{
                    echo $this->twig->render('404.html.twig', array('page' => 'page de shop inconnue'));
                }
            }
            elseif ($page == 'panier') {
                $ctrlPanier = new CtrlPanier($this->twig);
                $ctrlPanier->afficherPanier();
            }
            elseif ($page == 'produit') {
                $ctrlProduit = new CtrlProduit($this->twig,$_GET['product_id']);
                $ctrlProduit->afficherProduit();
            }
            else {
                echo $this->twig->render("accueil.html.twig");
            }
        }
        else { // comportement par défaut
            echo $this->twig->render("accueil.html.twig");
        }      

    }
}