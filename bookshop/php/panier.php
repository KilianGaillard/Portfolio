<?php

require_once 'bibli_bookshop.php';
require_once 'bibli_generale.php';

ob_start();
session_start();

if(!isset($_SESSION['panier'])){
    $_SESSION['panier']=[];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    if (isset($_POST['quantite'])) {
        // Cas du bouton "Ajouter au panier" depuis la fiche détails
        $quantite = intval($_POST['quantite']);
        for ($i = 0; $i < $quantite; $i++) {
            ajoutLivre($id);
        }
    } else if (isset($_POST['modif'])) {
        // Cas des boutons + et - dans le panier
        $modif = intval($_POST['modif']);
        modifQuantiteLivre($id, $modif);
    } else if(isset($_POST['suppr'])){
        supprimerLivre($id);
    }
    else { 
        // Cas de base, donc d'un click sur le bouton ajouter au panier à côté de l'image d'un livre
        ajoutLivre($id);
    }
    //On retourne après l'ajout au panier, à la page où l'on était, pour rendre plus naturel le fonctionnement 
    //de la page web
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}


//On n'accède évidemment à cette page qu'en étant connecté
if(!estAuthentifie()){
    header('Location: ./connexion.php');
    exit();
}

affDebutEnseigneEntete('BookShop | Mon panier');


$message_commande = '';
//envoi de la commande
//puis message de commande réussie ou non
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    if(envoiDeCommande()){    

        $message_commande = "Ajout de la commande réussi !";
    } else {
        $message_commande = "Erreur lors de l'ajout de la commande.";
    }
}

// affichage du message commande si défini
if ($message_commande !== '') {
    echo '<p style="color: green; font-weight: bold;">' . htmlspecialchars($message_commande) . '</p>';
}
afficherPanier();
affPiedFin();




function ajoutLivre($id){
    //si le livre est déjà présent dans le panier, on lui ajoute un exemplaire
    if(isset($_SESSION['panier'][$id])){
        $_SESSION['panier'][$id]['quantite']+=1;
        //sinon, on crée une entrée correspondant au nouveau livre
    }else {
        //requête sql pour récupérer le titre et le prix 
        $bd = bdConnect();
        $sql = "SELECT liTitre,liPrix FROM livre WHERE liID = $id";
        $res = bdSendRequest($bd,$sql);
        if ($tab = mysqli_fetch_assoc($res)) {
            $_SESSION['panier'][$id] = ['titre' => $tab['liTitre'], 'prix' => $tab['liPrix'], 'quantite' => 1];;
        }
        mysqli_free_result($res);
        mysqli_close($bd);
        
    }
}

function modifQuantiteLivre($id,$quantite){
    if(isset($_SESSION['panier'][$id])){

        if($quantite==1){ 
            // Cas de la quantité=1, donc potentiellement du click sur le bouton +
            $_SESSION['panier'][$id]['quantite']+=1;
        }
        else if($quantite==-1){
            // Cas de click sur le bouton -
            if($_SESSION['panier'][$id]['quantite']>1){
                $_SESSION['panier'][$id]['quantite']-=1;
            }
            else {
                //si l'on click sur -, mais qu'il n'y a qu'un exemplaire, on supprime le livre du panier
                supprimerLivre($id);
            }
        }
        else {
            //si la quantité est supérieure à 1, on ajoute le livre autant de fois qu'il le faut
            for ($i = 0; $i < $quantite; $i++) {
                ajoutLivre($id);
            }
        }
    }
}

function supprimerLivre($id){
    if(isset($_SESSION['panier'][$id])){
        unset($_SESSION['panier'][$id]);
    }
}

function envoiDeCommande(){
    if(!empty($_SESSION['panier'])){
        //attention à la définition de l'id client, pour les requêtes
        if (!isset($_SESSION['cliID']) || (int)$_SESSION['cliID'] <= 0) {
            echo "<p>Erreur : utilisateur non identifié.</p>";
            return;
        }
        $bd=bdConnect();


        // Récupération des informations d'adresse du client pour vérifier si toutes les infos ont été renseignées
        $cliID = (int)$_SESSION['cliID'];
        $sql = "SELECT cliAdresse, cliCP, cliVille, cliPays FROM client WHERE cliID = $cliID";
        $res = bdSendRequest($bd, $sql);

        if ($client = mysqli_fetch_assoc($res)) {
            // Vérifier que tous les champs d'adresse sont remplis
            if (empty($client['cliAdresse']) || empty($client['cliCP']) || empty($client['cliVille']) || empty($client['cliPays'])) {
                echo "<p style='color: red; font-weight: bold;'>Erreur : Vous devez renseigner votre adresse de livraison dans votre compte avant de passer commande.</p>";
                mysqli_close($bd);
                return false;
            }
        } else {
            echo "<p>Erreur : utilisateur non trouvé en base.</p>";
            mysqli_close($bd);
            return false;
        }
        mysqli_free_result($res);



        //préparation des variables pour la requête dans commande
        $coIDClient = $cliID;
        $coDate = date('YmdHi');
        //insertion dans commande 
        $sql="INSERT INTO commande (coIDClient, coDate)
                    VALUES ($coIDClient, $coDate)";
        $res=bdSendRequest($bd,$sql);

        //récupération de l'id de la commande que l'on vient d'insérer
        $ccIDCommande = mysqli_insert_id($bd);

        //insertion dans compo_commande 
         foreach ($_SESSION['panier'] as $ccIDLivre => $infos) {
            $ccQuantite = intval($infos['quantite']);
            $sql="INSERT INTO compo_commande (ccIDLivre, ccIDCommande,ccQuantite)
                    VALUES ($ccIDLivre, $ccIDCommande,$ccQuantite)";
            $res=bdSendRequest($bd,$sql);

         }
         $_SESSION['panier'] = [];
        mysqli_close($bd);
        return true;
    }
    return false;
}

function afficherPanier(){
    $totallivre=0;
    echo '<h2>Mon Panier</h2>';

    if (empty($_SESSION['panier'])) {
        //affichage du panier vide
        echo "<p>Votre panier est vide.</p>";
    } else {

        //affichage du bouton de l'envoi de commande
        echo '<table><tr><td><form method="post" action="panier.php">',
        '<button type="submit" name="valider_commande">Valider la commande</button>',
        '</form></td></tr>',
        '</table>'; 

        //haut du panier (description des colonnes de celui-ci)
        echo '<table class="panier">';
        echo '<tr><th>Livre</th><th>Titre</th><th>Prix unitaire</th><th>Quantité</th><th>Total de la ligne</th><th>Modification<br>quantité</th><th>Suppression du livre</th></tr>';

        //récupération des infos des livres du panier
        foreach($_SESSION['panier'] as $id => $livre){
            $titre = $livre['titre'];
            $prix  = $livre['prix'];
            $quantite = $livre['quantite'];
            // puis affichage de chaque livre
            if($quantite>0){
                echo '<tr>',
                        '<td>',
                            '<a href="details.php?article=', $id, '" title="Voir détails">',
                            '<img src="../images/livres/', $id, '_mini.jpg" alt="',$titre,'"></a>',
                        '</td>',
                        '<td>',$titre,'</td>',
                        '<td>',$prix,'€</td>',
                        '<td>',$quantite,'</td>',
                        '<td>',$quantite*$prix,'€</td>';
                $totallivre=$totallivre+$quantite*$prix;
            
        //bouton d'ajout ou de supression d'exemplaire de livre (les boutons qui se trouvent directement sur la ligne d'affichage)
                echo '<td>',
                                '<form method="post" action="panier.php" style="display:inline;">',
                                    '<input type="hidden" name="id" value="', $id, '">',
                                    '<input type="hidden" name="modif" value="1">',
                                    '<button type="submit">+</button>',
                                '</form>',
                                '<form method="post" action="panier.php" style="display:inline;">',
                                    '<input type="hidden" name="id" value="', $id, '">',
                                    '<input type="hidden" name="modif" value="-1">',
                                    '<button type="submit">-</button>',
                                '</form>',
                    '</td>',
                    '<td>',
                        '<form method="post" action="panier.php" style="display:inline;">',
                            '<input type="hidden" name="id" value="', $id, '">',
                            '<input type="hidden" name="suppr" value="1">',
                            '<button type="submit">Supprimer le livre</button>',
                        '</form>',
                    '</td>',
                '</tr>';
            }
        }
        echo '</table>';
        //affichage du total du panier
        echo'<table><tr><td>Prix total du panier : ',$totallivre,'€</td></tr></table>';


        
    
    }
}