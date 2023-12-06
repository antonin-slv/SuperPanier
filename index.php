<?php
// biblio.php
/* récupérer le tableau des données */
/* inclure l'autoloader */
require_once 'vendor/autoload.php';
/* templates chargés à partir du système de fichiers (répertoire vue) */
$loader = new Twig\Loader\FilesystemLoader('vue');
/* options : prod = cache dans le répertoire cache, dev = pas de cache */
$options_prod = array('cache' => 'cache', 'autoescape' => true);
$options_dev = array('cache' => false, 'autoescape' => true);
/* stocker la configuration */
$twig = new Twig\Environment($loader);
/* charger+compiler le template, exécuter, envoyer le résultat au navigateur */



if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'accueil';
}
//vérifie si la page existe
if (!file_exists("vue/$page.html.twig")) {
    $page = '404';
}
echo $twig->render("$page.html.twig");