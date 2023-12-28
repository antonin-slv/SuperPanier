<?php
require_once 'vendor/autoload.php'; //pour TWIG
include_once('CtrlPanier.php');
include_once('CtrlShop.php');
include_once('CtrlConnexion.php');
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

        /*
         *   GESTION DES SESSIONS (utilisateur connecté ou non)
         *   puis
         *   GESTION DU PANIER
         *   à faire dans l'initialisation de la SESSION ?
         */

        $this->gestionAction();//fonction qui fait gérer les actions (déconnexion, ajout au panier, etc...)

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if (!file_exists("vue/$page.html.twig") && !file_exists("controlleur/Ctrl$page.php"))
            {
                //on sort la page 404
                echo $this->twig->render('404.html.twig', array('page' => $page));
            }
            elseif ($page == 'shop') //si on est sur la page shop
            {  
                if (isset($_GET['product'])) {
                    $ctrlShop = new CtrlShop($this->twig, $_GET['product']);
                    $ctrlShop->afficherShop();
                } else {
                    echo $this->twig->render('404.html.twig', array('page' => 'shop (sans paramètre produit...)'));
                }
            }
            elseif ($page == 'panier') //si on est sur la page panier
            { 
                $ctrlPanier = new CtrlPanier($this->twig);
                $ctrlPanier->afficherPanier();
            }
            elseif ($page == 'produit') //si on est sur la page produit
            { 
                $ctrlProduit = new CtrlProduit($this->twig, $_GET['product_id']);
                $ctrlProduit->afficherProduit();

            }
            elseif ($page == 'connexion' || $page == 'deconnexion' || $page == 'register') //si on est sur la page connexion
            { 
                $ctrlConnexion = new CtrlConnexion($this->twig);
                //la page qui ressort est gérée en interne par le contrôlleur
                $ctrlConnexion->afficherPage();

            } else { // comportement par défaut (accueil)
                echo $this->twig->render("accueil.html.twig");
            }
        } else { // comportement par défaut
            echo $this->twig->render("accueil.html.twig");
        }
    }

    public function gestionAction() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'deconnexion') {
                session_destroy();
                header('Location: index.php');
            }
            
            elseif ($_GET['action'] == 'addToCart') {
                
                if (isset($_POST['quantity']) && isset($_POST['product_id']))
                {
                    
                    $panier = new Panier($_SESSION['Connected'],$_SESSION['user_id']);
                    //on met dans la BDD ce nombre de tel produit
                    $panier->addProduct($_POST['product_id'], $_POST['quantity']);
                }
            }
            elseif ($_GET['action'] == 'rmFromCart') {
                if (isset($_POST['product_id']))
                {
                    $panier = new Panier($_SESSION['Connected'],$_SESSION['user_id']);
                    $panier->removeProduct($_POST['product_id']);
                }
            }
        }
    }

    public function initSession()
    {
        session_start();
        //vérifie si la session est déjà initialisée
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = session_id();
            $_SESSION['Connected'] = false;
        }

        if (!isset($_SESSION['Panier'])) {
            $panier = new Panier($_SESSION['Connected'],$_SESSION['user_id']);
            $_SESSION['Panier'] = $panier->getProducts();
        }
    }
}
