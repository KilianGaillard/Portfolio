<?php

// chargement des bibliothèques de fonctions
require_once('bibli_bookshop.php');
require_once('bibli_generale.php');

// génération de la page

// ob_start(); // pas nécessaire

affDebutEnseigneEntete('BookShop | Inscription');

affContenuL();

affPiedFin();


/*********************************************************
 *
 * Définitions des fonctions locales de la page
 *
 *********************************************************/
//_______________________________________________________________
/**
 * Affichage du contenu principal de la page
 *
 * ATTENTION : dans cette fonction, il manque la protection des sorties avec htmlentities()
 *
 * @return  void
 */
function affContenuL() : void {
    echo    '<h1>Réception des données soumises</h1>',
            '<section>',
                '<h2>Avec une boucle foreach</h2>',
                '<ul>';

    foreach($_POST as $cle => $val){
        echo        '<li>cle = ', $cle, ', valeur = ', $val, '</li>';
    }

    echo        '</ul>',
            '</section>',
            '<section>',
                '<h2>Avec var_dump()</h2>',
                '<pre>';
    var_dump($_POST);
    echo        '</pre>',
            '</section>',
            '<section>',
                '<h2>Avec print_r()</h2>',
                '<pre>', print_r($_POST, true), '</pre>',
            '</section>';
}
