<?php
require_once 'bibli_bookshop.php';
require_once('bibli_generale.php');

ob_start();
session_start();
affichageDetailsLivre();
affPiedFin();


function affichageDetailsLivre(){

    $bd = bdConnect();

    //vérification de l'existence de l'ID du livre passé en paramètre
    if (isset($_GET['article'])) {
        $bookID = (int)$_GET['article'];  //transtype l'ID

        //requête pour récupérer les détails du livre
        $sql = "SELECT liTitre,liResume,liPrix,GROUP_CONCAT(DISTINCT CONCAT(auPrenom, '|', auNom) SEPARATOR '@') AS auteurs
        FROM livre 
        INNER JOIN editeur ON liIDEditeur = edID
        INNER JOIN aut_livre ON al_IDLivre = liID
        INNER JOIN auteur ON al_IDAuteur = auID
        WHERE liID = $bookID
        GROUP BY liID";
        $result = mysqli_query($bd, $sql);

        if (mysqli_num_rows($result) == 1) {
            $book = mysqli_fetch_assoc($result);
            $auteursBruts = explode('@', $book['auteurs']); // séparation entre chaque auteur
            $auteurs = [];
            foreach ($auteursBruts as $auteur) {
                list($prenom, $nom) = explode('|', $auteur);
                $auteurs[] = htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom);
            }
        } else {
            echo "Livre introuvable.";
            exit();
        }
    } else {
        echo "ID du livre manquant.";
        exit();
    }

//fermeture de la connexion à la base de données
    mysqli_free_result($result);
    mysqli_close($bd);

    $auteurString = implode(', ', $auteurs);
    affDebutEnseigneEntete('BookShop | Détail du livre');
    echo 
        '<section>',
            '<h2>Détail du livre "' . htmlspecialchars($book['liTitre']) . '"</h2>',
            '<div class="book-details">',
                '<p><strong>Auteur : </strong>' . $auteurString . '</p>',
                '<p><strong>Resume : </strong>' . nl2br(htmlspecialchars($book['liResume'])) . '</p>',
                '<p><strong>Prix : </strong>' . number_format($book['liPrix'], 2, ',', ' ') .  '€</p>',
                '<form method="post" action="./panier.php">',
                    '<input type="hidden" name="id" value="' . $bookID . '">',
                    '<label for="quantite">Quantité :</label>',
                    '<input type="number" name="quantite" id="quantite" min="1" value="1" required>',
                    '<input type="submit" name="btnAddToCart" value="Ajouter au panier">',
                '</form>',
            '</div>',
        '</section>',
    
        '<section>',
            '<p><a href="../index.php">Retour à la liste des livres</a></p>',
        '</section>';
}


