<?php
/** 2ème version : liste des livres, résumé et des auteurs */

require_once '../php/bibli_generale.php';
require_once '../php/bibli_bookshop.php';

ob_start(); //démarre la bufferisation

$bd = bdConnect();

$sql = 'SELECT liID, liTitre, liPrix, liNbPages, liResume, liISBN13, edNom, edWeb, auNom, auPrenom
        FROM livre, editeur, auteur, aut_livre
        WHERE liIDEditeur = edID
        AND liID = al_IDLivre
        AND auID = al_IDAuteur
        ORDER BY liID';

$res = bdSendRequest($bd, $sql);

affDebut('BookShop | Recherche');

$lastID = -1;
while ($t = mysqli_fetch_assoc($res)) {
    if ($t['liID'] != $lastID) {
        if ($lastID != -1) {
            affLivreL(htmlProtegerSorties($livre));
        }
        $lastID = $t['liID'];
        $livre = array( 'id' => $t['liID'],
                        'titre' => $t['liTitre'],
                        'edNom' => $t['edNom'],
                        'edWeb' => trim($t['edWeb']),
                        'resume' => $t['liResume'],
                        'pages' => $t['liNbPages'],
                        'ISBN13' => $t['liISBN13'],
                        'prix' => $t['liPrix'],
                        'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom'])));
    }
    else {
        $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
    }       
}
// libération des ressources
mysqli_free_result($res);
// déconnexion du serveur de bases de données
mysqli_close($bd);

if ($lastID != -1) {
    affLivreL(htmlProtegerSorties($livre));
}

affFin();

// fin du script --> envoi de la page 
ob_end_flush();

// ----------  Fonctions locales au script ----------- //

/**
 *  Affichage d'un livre.
 *
 * @param   array   $t  tableau associatif des infos sur le livre (id, auteurs(nom, prenom), titre, prix, pages, ISBN13, edWeb, edNom, resume)
 *
 * @return void
 */
function affLivreL(array $t): void {
    echo 
        '<p style="margin-top: 30px;">', 
            '<img src="../images/livres/', $t['id'], 
                '_mini.jpg" style="float: left; margin: 0 10px 10px 0; border: solid 1px #000; height: 100px;" alt="',
                $t['titre'], '">',
            '<strong>', $t['titre'], '</strong> <br>',
            'Écrit par : ';
    $i = 0;
    foreach ($t['auteurs'] as $auteur) {
        if ($i > 0) {
            echo ', ';
        }
        ++$i;
        echo $auteur['prenom'], ' ', $auteur['nom'];
    }
            
    echo    '<br>Éditeur : <a href="http://', $t['edWeb'], '" target="_blank">',
                $t['edNom'], '</a><br>',
            'Prix : ', $t['prix'], '<br>',
            'Pages : ', $t['pages'], '<br>',
            'ISBN13 : ', $t['ISBN13'], '<br>',
            'Résumé : <em>', $t['resume'], '</em>',
        '</p>';
        
}

