<?php

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

// critères de recherche si pas de paramètres reçus dans $_GET
$recherche = array('type' => 'auteur', 'quoi' => '');

if (isset($_GET['type']) && isset($_GET['quoi'])) {
    $oks = ['titre', 'auteur'];
    if (! in_array($_GET['type'], $oks)){
        $errs[] = 'La valeur du "type" doit être égale à "'.implode('" ou à "', $oks).'".';
    }
    $recherche['type'] = $_GET['type'];
    $recherche['quoi'] = trim($_GET['quoi']);
    $l1 = mb_strlen($recherche['quoi'], encoding:'UTF-8');
    if ($l1 < LMIN_CRITERE_RECHERCHE){
        $errs[] = 'Le critère de recherche doit contenir au moins '. LMIN_CRITERE_RECHERCHE . ' caractères.';
    }
    if ($l1 != mb_strlen(strip_tags($recherche['quoi']), encoding:'UTF-8')){
        $errs[] = 'Le critère de recherche ne doit pas contenir de tags HTML.';
    }
}

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/

affDebutEnseigneEntete('BookShop | Recherche');

affContenuL($recherche, $errs);

affPiedFin();

// fin du script --> envoi de la page 
ob_end_flush();


// ----------  Fonctions locales au script ----------- //

/**
 *  Contenu de la page : formulaire et résultats de la recherche
 *
 * @param array  $recherche     critères de recherche (type et quoi)
 * @param array  $erreurs       erreurs détectées dans l'URL
 *
 * @return void
 */
function affContenuL(array $recherche, array $erreurs) : void {


    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limite = 4; //limite de nombre de livres à afficher sur une page
    $offset = ($page - 1) * $limite;
    
    echo '<h3>Recherche par une partie du nom d\'un auteur ou du titre</h3>'; 
    
    /* choix de la méthode get pour avoir la même forme d'URL lors d'une soumission du formulaire, 
    et lorsqu'on accède à la page suite à un clic sur un nom d'un auteur */
    echo '<form action="', basename($_SERVER['PHP_SELF']), '" method="get">',
            // la protection des sorties avec htmlProtegerSorties() est réalisée au dernier moment
            // juste avant l'encapsulation du critère de recherche dans le code HTML
            '<p class="center">Rechercher <input type="text" name="quoi" minlength="2" value="', htmlProtegerSorties($recherche['quoi']), '">',
            ' dans ', 
                '<select name="type">', 
                    '<option value="auteur" ', $recherche['type'] == 'auteur' ? 'selected' : '', '>auteurs</option>', 
                    '<option value="titre" ', $recherche['type'] == 'titre' ? 'selected' : '','>titre</option>', 
                '</select>', 
            '<input type="submit" value="Rechercher">', // pas d'attribut name pour qu'il n'y ait pas d'élément correspondant
                                                        // au bouton submit dans l'URL lors de la soumission du formulaire
            '</p>', 
          '</form>';
    
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

    if ($recherche['quoi']){ //si recherche à faire en base de données
    
        // ouverture de la connexion, requête
        $bd = bdConnect();
        
        // la protection des entrées avec mysqli_real_escape_string() est réalisée au dernier moment
        // juste avant l'ajout du critère de recherche dans la requête SQL
        $q = mysqli_real_escape_string($bd, $recherche['quoi']);
        
        if ($recherche['type'] == 'auteur') {
            $critere = " WHERE liID in (SELECT al_IDLivre FROM aut_livre INNER JOIN auteur ON al_IDAuteur = auID WHERE auNom LIKE '%$q%')";
        } 
        else {
            $critere = " WHERE liTitre LIKE '%$q%'";    
        }

        $sqlCount = "SELECT COUNT(DISTINCT liID) AS nb
            FROM ((livre INNER JOIN editeur ON liIDEditeur = edID)
                  INNER JOIN aut_livre ON al_IDLivre = liID)
                  INNER JOIN auteur ON al_IDAuteur = auID
            $critere";

        $resCount = bdSendRequest($bd, $sqlCount);
        $nbTotal = (int)mysqli_fetch_assoc($resCount)['nb'];
        mysqli_free_result($resCount);
        $nbPages = ceil($nbTotal / $limite); //le nombre de pages à afficher, calculer sur le nombre de livres à afficher
                                             //diviser par la limite de livre par page, déterminer arbitrairement 

        $sql = "SELECT  liID, liTitre, liPrix, liNbPages, liISBN13, edNom, edWeb,
                        GROUP_CONCAT(CONCAT(auPrenom, '|', auNom) SEPARATOR '@') AS auteurs
                FROM    ((livre  INNER JOIN editeur ON liIDEditeur = edID)
                                 INNER JOIN aut_livre ON al_IDLivre = liID)
                                 INNER JOIN auteur ON al_IDAuteur = auID
                $critere
                GROUP BY liID
                ORDER BY liID
                LIMIT $limite OFFSET $offset";

        $res = bdSendRequest($bd, $sql);
        
        if (mysqli_num_rows($res) == 0){
            echo '<p>Aucun livre trouvé.</p>';
        }
        else{
            while ($t = mysqli_fetch_assoc($res)) {
                affLivreL($t);
            }
        }
        //système de pagination
        if ($nbPages > 1) {
            echo '<div><p>Page ', $page, ' sur ', $nbPages, '</p><p>';
            for ($i = 1; $i <= $nbPages; $i++) {
                if ($i == $page) {
                    echo " <strong>$i</strong> ";
                } else {
                    echo ' <a href="', basename($_SERVER['PHP_SELF']),
                        '?type=', urlencode($recherche['type']),
                        '&quoi=', urlencode($recherche['quoi']),
                        '&page=', $i, '">', $i, '</a> ';
                }
            }
            echo '</p></div>';
        }

        // libération des ressources
        mysqli_free_result($res);
        // déconnexion du serveur de bases de données
        mysqli_close($bd);
    }
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
            //formulaire pour l'ajout au panier
            '<form method="post" action="./panier.php" style="display:inline;">',
                    '<input type="hidden" name="id" value="',$livre['liID'],'">',
                    '<button type="submit" class="addToCart" title="Ajouter au panier"></button>',
            '</form>',
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

