<?php
/** 1ère version : liste des livres */

ob_start(); //démarre la bufferisation

require_once '../php/bibli_generale.php';
require_once '../php/bibli_bookshop.php';

$bd = bdConnect();

$sql = 'SELECT  liID, liTitre, liPrix, liNbPages, liISBN13, edNom, edWeb
        FROM    livre, editeur
        WHERE liIDEditeur = edID';

$res = bdSendRequest($bd, $sql);;

affDebut('BookShop | Recherche');

while ($t = mysqli_fetch_assoc($res)) {
    echo '<p> Livre #', $t['liID'], '</p>',
        '<ul>',
            '<li><strong>', htmlProtegerSorties($t['liTitre']), '</strong></li>',
            '<li>Édité par : <a href="http://', htmlProtegerSorties(trim($t['edWeb'])), '" target="_blank">',
            htmlProtegerSorties($t['edNom']), '</a></li>',
            '<li>Prix : ', $t['liPrix'], '&euro;</li>', 
            '<li>Pages : ', $t['liNbPages'], '</li>',
            '<li>ISBN13 : ', htmlProtegerSorties($t['liISBN13']), '</li>',
        '</ul>';
}

// libération des ressources
mysqli_free_result($res);
// déconnexion du serveur de bases de données
mysqli_close($bd);

affFin();

// fin du script --> envoi de la page 
ob_end_flush();


