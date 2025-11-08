<?php

require_once './php/bibli_generale.php';
require_once ('./php/bibli_bookshop.php');

// bufferisation des sorties
ob_start();

// démarrage ou reprise de la session
session_start();

affDebutEnseigneEntete('BookShop | Bienvenue', '.');

affContenuL();

affPiedFin();

// ----------  Fonctions locales au script ----------- //

/**
 *  Affichage du contenu de la page
 *
 * @return void
 */
function affContenuL() : void {
    echo
        '<h1>Bienvenue sur BookShop !</h1>',
        '<p>Passez la souris sur le logo et laissez-vous guider pour découvrir les dernières exclusivités de notre site. </p>',
        '<p>Nouveau venu sur BookShop ? Consultez notre <a href="./php/presentation.php">page de présentation</a> !</p>';
        //requête de dernier ajout : 
        //on récupère tout simplement les 4 derniers livres insérés dans la base de données
        $bd = bdConnect();
        $p = 'Voici les 4 derniers articles ajoutés dans notre boutique en ligne :';
        $sql = "SELECT liID,liTitre,GROUP_CONCAT(CONCAT(auPrenom, '|', auNom) SEPARATOR '@') AS auteurs
                FROM livre  
                INNER JOIN editeur ON liIDEditeur = edID
                INNER JOIN aut_livre ON al_IDLivre = liID
                INNER JOIN auteur ON al_IDAuteur = auID 
                GROUP BY liID         
                ORDER BY liID DESC 
                LIMIT 4";

        $derniersAjouts=recupererLivres($sql,$bd);
        affSectionLivresL('Dernières nouveautés', $p, $derniersAjouts);


        $p = 'Voici les 4 articles les plus commandés dans notre boutique en ligne :';
        //requête du plus commandé : 
        //on trie les livres par leur nombre de commandes, dans l'ordre décroissant et on récupère seulement 4 livres
        $sql2 = "SELECT liID,liTitre,GROUP_CONCAT(DISTINCT CONCAT(auPrenom, '|', auNom) SEPARATOR '@') AS auteurs,
                 SUM(compo_commande.ccQuantite) AS number_commandes

                FROM livre
                INNER JOIN editeur ON liIDEditeur = edID
                INNER JOIN aut_livre ON al_IDLivre = liID
                INNER JOIN auteur ON al_IDAuteur = auID 
                INNER JOIN compo_commande ON livre.liID = compo_commande.ccIDLivre 
                GROUP BY liID 
                ORDER BY number_commandes DESC 
                LIMIT 4";
    $meilleuresVentes=recupererLivres($sql2,$bd);
    affSectionLivresL('Top des ventes',$p, $meilleuresVentes);
}

function recupererLivres($sql, $bd) {
    $stmt = $bd->query($sql);
    if (!$stmt) {
        die('Erreur SQL : ' . $bd->error);
    }

    $livres = [];
    while ($row = $stmt->fetch_assoc()) {
        $livres[] = $row;
    }

    $result = [];

    foreach ($livres as $livre) {
        $listeAuteurs = [];
        if (!empty($livre['auteurs'])) {
            $auteursBruts = explode('@', $livre['auteurs']);
            foreach ($auteursBruts as $auteur) {
                $parts = explode('|', $auteur);
                if (count($parts) === 2) {
                    [$prenom, $nom] = $parts;
                    $listeAuteurs[] = ['prenom' => $prenom, 'nom' => $nom];
                }
            }
        }

        $result[] = [
            'id' => $livre['liID'],
            'titre' => $livre['liTitre'],
            'auteurs' => $listeAuteurs
        ];
    }

    return $result;
}




/**
 *  Affichage d'une section de livres
 *
 * @param  string  $h2         titre de la section (contenu de l'élément h2)
 * @param  string  $p          contenu de l'élément p
 * @param  array   $livres     tableau contenant un élément pour chaque livre (tableau associatif avec id, auteurs(nom, prenom), titre)
 *
 * @return void
 */
function affSectionLivresL(string $h2, string $p, array $livres): void {
    echo
        '<section>',
            '<h2>', $h2, '</h2>',
            '<p>', $p, '</p>';

    foreach ($livres as $livre) {
        echo
            '<figure>',
            //ce formulaire gère l'ajout au panier du livre cliqué
                '<form method="post" action="php/panier.php" style="display:inline;">',
                    '<input type="hidden" name="id" value="',$livre['id'],'">',
                    '<button type="submit" class="addToCart" title="Ajouter au panier"></button>',
                '</form>',
                '<a class="addToWishlist" href="#" title="Ajouter à la liste de cadeaux"></a>',
                '<a href="php/details.php?article=', $livre['id'], '" title="Voir détails"><img src="./images/livres/',
                $livre['id'], '_mini.jpg" alt="', $livre['titre'],'"></a>',
                '<figcaption>';
        $auteurs = $livre['auteurs'];
        $i = 0;
        foreach ($livre['auteurs'] as $auteur) {
            if ($i > 0) {
                echo ', ';
            }
            ++$i;
            echo    '<a title="Rechercher l\'auteur" href="php/recherche.php?type=auteur&amp;quoi=', urlencode($auteur['nom']), '">',
                    mb_substr($auteur['prenom'], 0, 1, encoding:'UTF-8'), '. ', $auteur['nom'], '</a>';
        }
        echo        '<br>',
                    '<strong>', $livre['titre'], '</strong>',
                '</figcaption>',
            '</figure> '; // ajout de l'espace pour obtenir un rendu identique à celui du fichier index_TP3.html
    }
    echo
        '</section>';
}
