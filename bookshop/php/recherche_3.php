<?php
/**
3ème version :  afficher la liste des livres d'un auteur dont le nom est passé dans l'URL.
Exemple : pour obtenir les livres écrits par Alan Moore, il faut utiliser l'URL :
recherche_3.php?type=auteur&quoi=Moore
*/

/* ------------------------------------------------------------------------------
    Architecture de la page
    - étape 1 : vérification des paramètres reçus dans l'URL
    - étape 2 : génération du code HTML de la page
------------------------------------------------------------------------------*/

require_once '../php/bibli_generale.php';
require_once '../php/bibli_bookshop.php';

// bufferisation des sorties
ob_start();

// démarrage ou reprise de la session
session_start();

/*------------------------- Etape 1 --------------------------------------------
- vérification des paramètres reçus dans l'URL
------------------------------------------------------------------------------*/

// erreurs détectées dans l'URL
$errs = [];

// nom de l'auteur dont on recherche les livres
$quoi = '';

if (! parametresControle('get', array('type', 'quoi'))){
    $errs[] = 'L\'URL doit être de la forme "' . basename($_SERVER['PHP_SELF']) .'?type=auteur&quoi=Moore".';
}
else{
    if ($_GET['type'] != 'auteur'){
        $errs[] = 'La valeur du "type" doit être égale à "auteur".';
    }
    $quoi = trim($_GET['quoi']);
    $l1 = mb_strlen($quoi, encoding:'UTF-8');
    if ($l1 != mb_strlen(strip_tags($quoi), encoding:'UTF-8')){
        $errs[] = 'Le nom de l\'auteur ne doit pas contenir de tags HTML.';
    }
}

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/


affDebutEnseigneEntete('BookShop | Recherche');

affContenuL($quoi, $errs);

affPiedFin();

// fin du script --> envoi de la page 
ob_end_flush();


// ----------  Fonctions locales au script ----------- //

/**
 *  Contenu de la page : résultats de la recherche, ou erreurs
 *
 * @param string $quoi      nom de l'auteur recherché
 * @param array  $erreurs   erreurs détectées dans l'URL
 *
 * @return void
 */
function affContenuL(string $quoi, array $erreurs): void {
    
    if ($erreurs) {
        $nbErr = count($erreurs);
        $pluriel = $nbErr > 1 ? 's':'';
        echo '<p class="error">',
                '<strong>Erreur',$pluriel, ' détectée', $pluriel, ' :</strong>';
        for ($i = 0; $i < $nbErr; $i++) {
                echo '<br>', $erreurs[$i];
        }
        echo '</p>';
        return; // ===> Fin de la fonction
    }

    // ouverture de la connexion, requête
    $bd = bdConnect();
    
    // la protection des entrées avec mysqli_real_escape_string() est réalisée au dernier moment
    // juste avant l'ajout du critère de recherche dans la requête SQL
    $q = mysqli_real_escape_string($bd, $quoi); // protection des entrées
    
    $sql = "SELECT  liID, liTitre, liPrix, liNbPages, liISBN13, edNom, edWeb,
                    GROUP_CONCAT(CONCAT(auPrenom, '|', auNom) SEPARATOR '@') AS auteurs
            FROM ((livre  INNER JOIN editeur ON liIDEditeur = edID)
                          INNER JOIN aut_livre ON al_IDLivre = liID)
                          INNER JOIN auteur ON al_IDAuteur = auID
            WHERE liID in (SELECT al_IDLivre FROM aut_livre INNER JOIN auteur ON al_IDAuteur = auID WHERE auNom = '$q')
            GROUP BY liID
            ORDER BY liID";

    $res = bdSendRequest($bd, $sql);
    
    echo '<h3>Livre(s) écrit(s) par l\'auteur de nom "', htmlProtegerSorties($quoi), '"</h3>';

    if (mysqli_num_rows($res) == 0){
        echo '<p>Aucun livre trouvé.</p>';
    }
    else{
        while ($t = mysqli_fetch_assoc($res)) {
            affLivreL($t);
        }
    }
    // libération des ressources
    mysqli_free_result($res);
    // déconnexion du serveur de bases de données
    mysqli_close($bd);
}   
    
/**
 * Affichage d'un livre.
 *
 * @param  array       $livre      tableau associatif les infos sur un livre (liID, liTitre, liPrix, liNbPages, liISBN13, edNom, edWeb, auteurs)
 *
 * @return void
 */
function affLivreL(array $livre) : void {
    // Le nom de l'auteur doit être encodé avec urlencode() avant d'être placé dans une URL, sans être passé auparavant par htmlentities()
    $auteurs = explode('@', $livre['auteurs']);
    unset($livre['auteurs']);
    $livre['edWeb'] = trim($livre['edWeb']);
    $livre = htmlProtegerSorties($livre);
    echo 
        '<article class="arRecherche">', 
            // TODO : à modifier pour le projet  
            '<a class="addToCart" href="#" title="Ajouter au panier"></a>',
            '<a class="addToWishlist" href="#" title="Ajouter à la liste de cadeaux"></a>',
            '<a href="details.php?article=', $livre['liID'], '" title="Voir détails"><img src="../images/livres/', $livre['liID'], '_mini.jpg" alt="',
            $livre['liTitre'],'"></a>',
            '<h5>', $livre['liTitre'], '</h5>',
            'Écrit par : ';
    $i = 0;
    foreach ($auteurs as $auteur) {
        list($prenom, $nom) = explode('|', $auteur);
        echo $i > 0 ? ', ' : '', '<a href="', basename($_SERVER['PHP_SELF']),'?type=auteur&amp;quoi=', urlencode($nom), '">',
        htmlProtegerSorties($prenom), ' ', htmlProtegerSorties($nom) ,'</a>';
        ++$i;
    }
            
    echo    '<br>Éditeur : <a class="lienExterne" href="http://', $livre['edWeb'], '" target="_blank">', $livre['edNom'], '</a><br>',
            'Prix : ', $livre['liPrix'], ' &euro;<br>',
            'Pages : ', $livre['liNbPages'], '<br>',
            'ISBN13 : ', $livre['liISBN13'],
        '</article>';
}
