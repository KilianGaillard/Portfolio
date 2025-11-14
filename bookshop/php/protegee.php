<?php

require_once 'bibli_generale.php';
require_once 'bibli_bookshop.php';

// bufferisation des sorties
ob_start();

// démarrage ou reprise de la session
session_start();


// si l'utilisateur n'est pas authentifié, on le redirige sur la page index.php
if (! estAuthentifie()){
    header('Location: ../index.php');
    exit;
}

// affichage de l'entête
affDebutEnseigneEntete('BookShop | Accès restreint');


$bd = bdConnect();

$sql = "SELECT *
        FROM client
        WHERE cliID = {$_SESSION['cliID']}";

$res = bdSendRequest($bd, $sql);

$T = mysqli_fetch_assoc($res);

mysqli_free_result($res);
mysqli_close($bd);

$T = htmlProtegerSorties($T);

echo
        '<section>',
            '<h2>Accès restreint aux utilisateurs authentifiés</h2>',
            '<ul>',
                '<li><strong>cliID : ', $_SESSION['cliID'], '</strong></li>',
                '<li>SID : ', session_id(), '</li>';
foreach($T as $cle => $val){
    echo        '<li>', $cle, ' : ', $val, '</li>';
}
echo        '</ul>',
        '</section>';

// affichage du pied de page
affPiedFin();

// facultatif car fait automatiquement par PHP
ob_end_flush();
