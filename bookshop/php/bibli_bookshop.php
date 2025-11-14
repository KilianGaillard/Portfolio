<?php
/*********************************************************
 *        Bibliothèque de fonctions spécifiques          *
 *        à l'application BookShop                       *
 *********************************************************/

// Force l'affichage des erreurs
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting( E_ALL );


define('IS_DEV', true);  //true en phase de développement, false en phase de production

define('BD_SERVER', 'mariadb-hostname'); // nom d'hôte ou adresse IP du serveur de base de données
define('BD_NAME', 'bookshop_db'); // nom de la base sur le serveur de base de données
define('BD_USER', 'bookshop_user'); // nom de l'utilisateur de la base
define('BD_PASS', 'bookshop_pass'); // mot de passe de l'utilisateur de la base

define('LMIN_CRITERE_RECHERCHE', 2);


// limites liées aux tailles des champs de la table client
define('LMAX_NOM', 50);         // taille du champ cliNom de la table client
define('LMAX_PRENOM', 50);      // taille du champ cliPrenom de la table client
define('LMAX_EMAIL', 100);      // taille du champ cliEmail de la table client
define('LMAX_TELEPHONE', 14);   // taille du champ cliTelephone de la table client

define('AGE_MINIMUM', 15);

define('LMIN_PASSWORD', 4);

//_______________________________________________________________
/**
 * Affichage du début de la page HTML (jusqu'à l'élément header inclus).
 *
 * @param  string  $titre       le titre de la page
 * @param  string  $prefixe     chemin relatif vers la racine du site
 *
 * @return void
 */
function affDebutEnseigneEntete(string $titre, string $prefixe = '..') : void {
    affDebut($titre, "$prefixe/styles/bookshop.css");
    echo
        '<main>',
            '<aside>',
                '<a href="http://www.facebook.com" target="_blank"></a>',
                '<a href="http://www.twitter.com" target="_blank"></a>',
                '<a href="http://plus.google.com" target="_blank"></a>',
                '<a href="http://www.pinterest.com" target="_blank"></a>',
            '</aside>',

            '<header>',
            '<nav>',
                '<a href="', $prefixe, '/index.php" title="Retour à la page d\'accueil"></a>';

    $liens = array( 'recherche'   => array( 'position' => 1, 'title' => 'Effectuer une recherche'),
                    'panier'      => array( 'position' => 2, 'title' => 'Voir votre panier'),
                    'liste'       => array( 'position' => 3, 'title' => 'Voir une liste de cadeaux'),
                    'compte'      => array( 'position' => 4, 'title' => 'Consulter votre compte'),
                    'deconnexion' => array( 'position' => 5, 'title' => 'Se déconnecter'));

    if (!estAuthentifie()){
        unset($liens['compte']);
        unset($liens['deconnexion']);
        ++$liens['recherche']['position'];
        ++$liens['panier']['position'];
        ++$liens['liste']['position'];
        // TODO : Peut-on implémenter les 3 incrémentations ci-dessus avec un foreach ?
        $liens['connexion'] = array( 'position' => 5, 'title' => 'Se connecter');
//         // Debug :
//         echo '<pre>', print_r($liens, true), '</pre>';
//         exit;
    }

    foreach ($liens as $cle => $val) {
        echo '<a class="pos', $val['position'], '" href="', $prefixe, '/php/', $cle, '.php" title="', $val['title'], '"></a>';
    }

    echo    '</nav>',
            '<img src="', $prefixe, '/images/soustitre.png" alt="sous titre">',
            '</header>';
}

//_______________________________________________________________
/**
 * Affichage du pied et de la fin de la page (de l'élément footer jusqu'à la fin)
 *
 * @return void
 */
function affPiedFin() : void {
    echo
        '<footer>',
            'BookShop &amp; Partners &copy; ', date('Y'), ' - ',
            '<a href="#">A propos</a> - ',
            '<a href="#">Emplois @ BookShop</a> - ',
            '<a href="#">Conditions d\'utilisation</a>',
        '</footer>',
    '</main>';

    affFin();
}

//_______________________________________________________________
/**
* Détermine si l'utilisateur est authentifié
*
* @return bool     true si l'utilisateur est authentifié, false sinon
*/
function estAuthentifie(): bool {
    return  isset($_SESSION['cliID']);
}


//_______________________________________________________________
/**
 * Termine une session et effectue une redirection vers la page transmise en paramètre
 *
 * Cette fonction est appelée quand l'utilisateur se déconnecte "normalement" et quand une
 * tentative de piratage est détectée. On pourrait améliorer l'application en différenciant ces
 * 2 situations. Et en cas de tentative de piratage, on pourrait faire des traitements pour
 * stocker par exemple l'adresse IP, etc.
 *
 * @param string    $page URL de la page vers laquelle l'utilisateur est redirigé
 *
 * @return void
 */
function sessionExit(string $page = '../index.php'): void {

    // suppression de toutes les variables de session
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        // suppression du cookie de session
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 86400,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    header("Location: $page");
    exit();
}
