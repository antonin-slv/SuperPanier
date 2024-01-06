<?php
require_once 'vendor/autoload.php'; //pour TWIG
include_once('CtrlPanier.php');
include_once('CtrlShop.php');
include_once('CtrlConnexion.php');
include_once('CtrlProduit.php');
include_once('CtrlAdmin.php');
include_once('CtrlPaiement.php');
include_once('CtrlFacture.php');

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
            if (!file_exists("vue/$page.html.twig") && !file_exists("controlleur/Ctrl$page.php") && $page != 'facture')
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
                $ctrlPanier = new CtrlPanier($this->twig,$_SESSION['Connected'],$_SESSION['user_id']);
                $ctrlPanier->afficherPanier();
            }
            elseif ($page == 'produit') //si on est sur la page produit
            { 
                $ctrlProduit = new CtrlProduit($this->twig, $_GET['product_id']);
                $ctrlProduit->afficherProduit();

            }
            elseif ($page == 'connexion' || $page == 'deconnexion' || $page == 'register') //si on est sur la page connexion
            { 
                if (!isset($GLOBALS['ctrlCo'])) $GLOBALS['ctrlCo'] = new CtrlConnexion($this->twig);
                
                //la page qui ressort est gérée en interne par le contrôleur
                $GLOBALS['ctrlCo']->afficherPage($page);
                
            }
            elseif ($page == 'admin') //si on est sur la page admin
            { 
                $ctrlAdmin = new CtrlAdmin($this->twig);
                if(isset($_GET['commande'])) //si on veut afficher une commande en particulier
                    $ctrlAdmin->afficherCommande($_GET['commande']);
                else //si on veut afficher la liste des commandes (par défaut
                    $ctrlAdmin->afficherListeCommandes();
            }
            elseif ($page == 'paiement') //si on est sur la page paiement
            { 
                if ($_SESSION['Connected']){
                    $CtrlPaiement = new CtrlPaiement($this->twig, $_SESSION['user_id']);
                    $CtrlPaiement->afficherPaiement();
                }
                else {
                    echo $this->twig->render('404.html.twig', array('page' => 'paiement (sans connexion...)'));
                }
            }
            elseif ($page == 'facture') //si on est sur la page facture
            {
                if (isset($_GET['commande'])) {
                    $CtrlFacture = new CtrlFacture($_GET['commande']);
                    $CtrlFacture->afficherFacture();
                }
                else {
                    echo $this->twig->render('404.html.twig', array('page' => 'facture (commande inexistante...)'));
                }
            }
            else { // comportement par défaut (accueil)
                echo $this->twig->render("accueil.html.twig");
            }
        } else { // comportement par défaut
            echo $this->twig->render("accueil.html.twig");
        }
    }

    public function gestionAction() {

        //est donc forcément initialisé ce monsieur :
        $GLOBALS['ctrlCo'] = new CtrlConnexion($this->twig);
        if (isset($_POST['action'])) {
            //connexion ou inscription
            if ($_POST['action'] == 'connexion') {
                //les fcts de connexion et d'inscription sont gérées par le contrôleur
                //elles utilisent directement le POST (pas de paramètres)
                if ($GLOBALS['ctrlCo']->connexion())
                {
                    $_GET['page'] = 'accueil';
                    $_POST['action'] = 'syncPanier';
                    if (isset($_SESSION['admin'])) {
                        if ($_SESSION['admin'] == true)
                        {
                            $_GET['page'] = 'admin';
                            $_POST['action'] = null;
                        }

                    }
                    
                }
                else {
                    $_GET['page'] = 'connexion';
                    $GLOBALS['ctrlCo']->error = "login";
                }
            }
            elseif ($_POST['action'] == 'register') {
                if($GLOBALS['ctrlCo']->register())
                {
                    $_GET['page'] = 'connexion';
                    $_POST['action'] = 'syncPanier';
                    //on passe page a false pour afficher la page de connexion
                    $GLOBALS['ctrlCo']->page = false;
                }
                else {
                    $_GET['page'] = 'register';
                    $GLOBALS['ctrlCo']->error = "register";
                }
            }
            elseif ($_POST['action'] == "ValiderCommande") {
                if (isset($_SESSION['admin'])) 
                {
                    if ($_SESSION['admin'] == true) {
                        $commande = new commande(intval($_GET['commande']));
                        $commande->validate();
                    }
                }
            }

            if ($_POST['action'] == 'syncPanier') // l'utilsateur vient de se connecter eque ce n'est pas un admin
            {
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

                    if ($_SESSION['Connected'] ){

                        $vraispanier = new Panier(true);
                        $vraispanier->setPanierID($_SESSION['user_id']);
                        $vraispanier->addCartToCart($panier->id);
                        $panier->killCart();
                        $_SESSION['Panier'] = $vraispanier->loadBDDProducts();
                        $_SESSION['Panier']['id'] = $vraispanier->id;
                    } 
                    else {
                        $panier->fromGuestToUser($_SESSION['futur_user_id']);
                        $panier->setPanierID($_SESSION['user_id']);
                        $_SESSION['Panier']['id'] = $panier->id;
                    }
                    
                }
                
            }
        }

        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'deconnexion') {
                $_SESSION = array();
                header('Location: index.php');
            }
            
            elseif ($_GET['action'] == 'addToCart') {
                
                if (isset($_POST['quantity']) && isset($_POST['product_id']))
                {   
                    $panier = new Panier($_SESSION['Connected']);

                    //au cas où le panier n'existe pas encore
                    if (isset($_SESSION['Panier']['id'])) $panier->id = $_SESSION['Panier']['id'];
                    else $panier->setPanierID($_SESSION['user_id']);
                    //on met dans la BDD ce nombre de tel produit
                    $panier->addProduct($_POST['product_id'], $_POST['quantity']);

                }
            }
            elseif ($_GET['action'] == 'rmFromCart') {
                if (isset($_POST['product_id']))
                {
                    $panier = new Panier($_SESSION['Connected']);
                    $panier->id = $_SESSION['Panier']['id'];
                    $panier->removeProduct($_POST['product_id']);
                }
            }
            elseif ($_GET['action'] == 'killCart') {
                $panier = new Panier($_SESSION['Connected']);
                $panier->killCart();
                unset($_SESSION['Panier']);
            }
            elseif ($_GET['action'] == 'modifierAdresseLivraison') {
                $paiement = new paiement();
                $paiement->saveAdresseLivraisonInBdd($_POST, $_SESSION['user_id']);
            }
            elseif ($_GET['action'] == 'modifierAdresseFacturation') {
                $paiement = new paiement();
                $paiement->saveAdresseFacturationInBdd($_POST, $_SESSION['user_id']);
            }
            elseif ($_GET['action'] == 'modifierModePaiement') {
                $paiement = new paiement();
                $paiement->saveModePaiementInBdd($_POST['payment_type'], $_SESSION['user_id']);
            }
            elseif ($_GET['action'] == 'payerEtFacture') {
                $paiement = new paiement($_SESSION['user_id']);
                $paiement->payerEtFacturer($_SESSION['user_id']);
                unset($_SESSION['Panier']);
            }
            elseif ($_GET['action'] == 'killCommande') {
                $panier = new panier(intval($_POST['idCommande']));
                $panier->killCart();
            }
            elseif ($_GET['action'] == 'addToStock') {
                $produit = new produit();
                $produit->addToStock(intval($_POST['product_id']),intval($_POST['quantity']));
            }
        }


        // On met en places les variables globales pour TWIG
        $this->twig->addGlobal('user', Array('connected' => $_SESSION['Connected'], 'id' => $_SESSION['user_id']));
        if (isset($_SESSION['admin'])){
            if ($_SESSION['admin'] == true) $this->twig->addGlobal('admin', true);
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
        
        
    }
}
