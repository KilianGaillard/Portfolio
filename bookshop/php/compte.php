<?php

require_once 'bibli_bookshop.php';
require_once 'bibli_generale.php';

ob_start();
session_start();
$bd = bdConnect();

//attribution des données de l'utilisateur connecté à la variable $client
$client = recuperationCompte($bd);

//traitement des formulaires
$msgIP = '';//chaîne de caractères affichée lors du traitement de l'update du formulaire d'informations personnelles
$msgAdr = '';//chaîne de caractères affichée lors du traitement de l'update du formulaire d'adresse
$errsIP = [];//tableau d'erreurs liées au formulaire d'informations personnelles
$errsA = [];//tableau d'erreurs liées au formulaire d'adresse


if (isset($_POST['updateInfos'])) {
    //on assigne un message de réussite ou non de l'update à $msgIP
    if(!$msgIP = traitementUpdateInfosPersonnelles($bd, $client)){
        //si une erreur est détectée lors de la vérfication du formulaire infos personnelles
        //on n'affichera pas de message supplémentaire (celui-ci étant déjà assuré par l'affichage 
        //du formulaire lorsqu'il y a des erreurs)
        $msgIP='';
    }
    $errsIP = traitementErreursInfosPersonnelles($client);
}

if (isset($_POST['updateAdresse'])) {
    //on assigne un message de réussite ou non de l'update à $msgIP
    if(!$msgAdr = traitementUpdateAdresse($bd, $client)){
        $msgAdr='';
    }
    $errsA = traitementErreursAdresse($client);
}

affDebutEnseigneEntete('BookShop | Mon compte');

echo '<h2>Mon Compte</h2>';

affichageFormulaireInfosPersonnelles($errsIP, $client, $msgIP);
affichageFormulaireAdresse($errsA, $client, $msgAdr);
affichageHistoriqueCommande($bd,$client);

mysqli_close($bd);
affPiedFin();

//méthode servant à la récupération des données de l'utlisateur connecté
function recuperationCompte($bd){
    if (!isset($_SESSION['cliID'])) {
        header('Location: connexion.php');
        exit();
    }
    //requête de récupération des données
    $cliID = (int)$_SESSION['cliID'];
    $sql = "SELECT cliNom, cliPrenom, cliTelephone, cliEmail, cliAdresse, cliCP, cliVille, cliPays FROM client WHERE cliID = $cliID";
    $res = bdSendRequest($bd, $sql);
    if (mysqli_num_rows($res) != 1) {
        echo "Client introuvable.";
        exit();
    }
    return mysqli_fetch_assoc($res);
}

function traitementUpdateInfosPersonnelles($bd, $client){
    $cliID = (int)$_SESSION['cliID'];
    if (isset($_POST['updateInfos'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $tel = trim($_POST['tel']); // <-- conserve la version avec les points
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $mdp = isset($_POST['pass1']) ? trim($_POST['pass1']) : '';

        if ($nom && $prenom && $tel && $email) {
            $errs = traitementErreursInfosPersonnelles($client);
            if (!empty($errs)) {
                return false;  // retourne false pour ne pas avoir de message supplémentaire
                               // lors de l'affichage du formulaire
            }

            $sqlUpdate = "UPDATE client SET cliNom='$nom', cliPrenom='$prenom', cliTelephone='$tel', cliEmail='$email'";

            if (!empty($mdp)) {
                $mdp = password_hash($mdp, PASSWORD_DEFAULT);
                $sqlUpdate .= ", cliPassword='$mdp'";
            }

            $sqlUpdate .= " WHERE cliID='$cliID'";
            bdSendRequest($bd,$sqlUpdate);

            // mise à jour du tableau $client pour l'affichage
            $client['cliNom'] = $nom;
            $client['cliPrenom'] = $prenom;
            $client['cliTelephone'] = $tel;
            $client['cliEmail'] = $email;
            return '✅ Informations personnelles mises à jour.';
        } else {
            return '❌ Tous les champs sont requis (hors mot de passe).';
        }
    }
}

function traitementUpdateAdresse($bd, &$client){
    $cliID = (int)$_SESSION['cliID'];

    if (isset($_POST['updateAdresse'])) {
        $adresse = trim($_POST['adresse']);
        $cp = trim($_POST['cp']);
        $ville = trim($_POST['ville']);
        $pays = trim($_POST['pays']);
    
        if ($adresse && $cp && $ville && $pays) {

            $errs = traitementErreursAdresse($client);
            if (!empty($errs)) {
                return false;  
            }

            $sql = "UPDATE client SET cliAdresse='$adresse', cliCP='$cp', cliVille='$ville', cliPays='$pays' WHERE cliID='$cliID'";
            bdSendRequest($bd,$sql);
    
            $client['cliAdresse'] = $adresse;
            $client['cliCP'] = $cp;
            $client['cliVille'] = $ville;
            $client['cliPays'] = $pays;
            return '✅ Adresse de livraison mise à jour.';
        } else {
            return '❌ Tous les champs sont requis.';
        }
    }
}


// méthode pour les erreurs du formulaire d'infos personnelles
function traitementErreursInfosPersonnelles($client) : array {
    $errs = [];

    if (isset($_POST['tel'])) {
        $tel = trim($_POST['tel']);

        if (!preg_match('/^0[1-9](\.\d{2}){4}$/', $tel)) {
            $errs[] = 'Vous devez saisir un numéro de téléphone valable (de la forme 05.43.65.67.90).';
        }
    }

    if(isset($_POST['email'])){
        $email= trim($_POST['email']);
        verifierTexte($email, 'L\'adresse email', $errs, LMAX_EMAIL);
        if(! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errs[] = 'L\'adresse email n\'est pas valide.';
        }
    }

    return $errs;
}

// méthode pour les erreurs du formulaire d'adresse
function traitementErreursAdresse($client) : array {
    $errs = [];
    if(strlen($_POST['pays'])>44){
        $errs[] = 'Vous devez saisir un nom de pays valable (en français, et dans sa version courte).';
    }
    if(strlen($_POST['ville'])>45){
        $errs[] = 'Vous devez saisir un nom de ville valable (en français, et dans sa version courte).';
    }
    if (strlen($_POST['cp']) != 5) {
        $errs[] = 'Vous devez saisir un code postal valable (5 caractères).';
    }
    return $errs;
}



//méthode pour l'affichage du formulaire
//3 paramètres : un tableau d'erreurs, un tableau asssociatif des infos du client
//et une chaîne de caractères contenant un message de réussite ou non de l'update

function affichageFormulaireInfosPersonnelles(?array $errs, $client, $msg = '') {
    if (is_string($msg) && $msg !== '') {
        echo '<p>', htmlspecialchars($msg), '</p>';
    }
    echo '<form method="post" action="', basename($_SERVER['PHP_SELF']), '">',
        '<section>';
            if (!empty($errs)) {
                echo '<p class="error"><strong>Les erreurs suivantes ont été relevées :</strong>';
                foreach ($errs as $e) {
                    echo '<br>- ', htmlspecialchars($e);
                }
                echo '</p>';
            }
            echo '<h3>Informations personnelles</h3>',
                '<table>';
                    affLigneInput('Nom : ', ['type' => 'text', 'name' => 'nom', 'value' => $client['cliNom'], 'required' => null]);
                    affLigneInput('Prenom : ', ['type' => 'text', 'name' => 'prenom', 'value' => $client['cliPrenom'], 'required' => null]);
                    affLigneInput('Telephone : ', ['type' => 'text', 'name' => 'tel', 'value' => $client['cliTelephone'], 'required' => null]);
                    affLigneInput('Email : ', ['type' => 'email', 'name' => 'email', 'value' => $client['cliEmail'], 'required' => null]);
                    affLigneInput('Mot de passe : ', ['type' => 'password', 'name' => 'pass1', 'value' => null, 'placeholder' => 'Laisser vide pour ne pas changer']);
            echo '</table>',
                '<p><input type="submit" class="btnUpdate" name="updateInfos" value="Mettre à jour"></p>',
            '</section>',
        '</form>';
}

function affichageFormulaireAdresse(?array $errs, $client, $msg = ''){
    if (is_string($msg) && $msg !== '') {
        echo '<p>', htmlspecialchars($msg), '</p>';
    }
    echo '<form method="post" action="', basename($_SERVER['PHP_SELF']), '">',
        '<section>';
            if (!empty($errs)) {
                echo '<p class="error"><strong>Les erreurs suivantes ont été relevées :</strong>';
                foreach ($errs as $e) {
                    echo '<br>- ', htmlspecialchars($e);
                }
                echo '</p>';
            }
            echo '<h3>Adresse de livraison</h3>',
                '<table>';
                    affLigneInput('Adresse : ', ['type' => 'text', 'name' => 'adresse', 'value' => $client['cliAdresse'], 'required' => null]);
                    affLigneInput('Code Postal : ', ['type' => 'text', 'name' => 'cp', 'value' => $client['cliCP'], 'required' => null]);
                    affLigneInput('Ville : ', ['type' => 'text', 'name' => 'ville', 'value' => $client['cliVille'], 'required' => null]);
                    affLigneInput('Pays : ', ['type' => 'text', 'name' => 'pays', 'value' => $client['cliPays'], 'required' => null]);
            echo '</table>',
                '<p><input type="submit" class="btnUpdate" name="updateAdresse" value="Mettre à jour"></p>',
            '</section>',
        '</form>';
}



function affichageHistoriqueCommande($bd,$client){
    $cliID = (int)$_SESSION['cliID'];
    echo '<h2>Historique de vos commandes</h2>';

    $sql = "SELECT coID, coIDClient, coDate
    FROM commande INNER JOIN compo_commande ON coID = ccIDCommande
                  INNER JOIN livre ON ccIDLivre = liID
                  WHERE coIDClient = $cliID
                  GROUP BY coID
                  ORDER BY coID";
    $res = bdSendRequest($bd,$sql);

    if (mysqli_num_rows($res) == 0) {
        echo '<p>Vous n\'avez pas encore passé de commande.</p>';
        return;

    }
    else{

        while($commande = mysqli_fetch_assoc($res)){


            //traitement de la date et de l'heure 

            $dateHeureBrute = $commande['coDate'];
            $annee = substr($dateHeureBrute, 0, 4);
            $mois = substr($dateHeureBrute, 4, 2);
            $jour = substr($dateHeureBrute, 6, 2);
            $heure = substr($dateHeureBrute, 8, 2);
            $minute = substr($dateHeureBrute, 10, 2);

            $dateAffichee = "$jour/$mois/$annee à ${heure}h${minute}";

            echo '<section>',
                    '<h3>Commande du '. $dateAffichee . '</h3>',
                    '<p>Détail de la commande : ';



            $coID = (int)$commande['coID'];
            $sql2 = "SELECT  liID, liTitre, liPrix, liNbPages, liISBN13, edNom, edWeb,ccQuantite,
                        GROUP_CONCAT(CONCAT(auPrenom, '|', auNom) SEPARATOR '@') AS auteurs
                    FROM    ((livre  INNER JOIN editeur ON liIDEditeur = edID)
                                 INNER JOIN aut_livre ON al_IDLivre = liID)
                                 INNER JOIN auteur ON al_IDAuteur = auID
                                 INNER JOIN compo_commande ON ccIDLivre = liID
                    WHERE ccIDCommande = $coID
                    GROUP BY liID
                    ORDER BY liID";

            $res2 = bdSendRequest($bd, $sql2);
            $prixTotal = 0;
            while ($t = mysqli_fetch_assoc($res2)) {
                $prixTotal += $t['liPrix'] * $t['ccQuantite'];
                affLivreL($t);
            }

            echo '<p><strong>Prix total : ', number_format($prixTotal, 2, ',', ' '), ' €</strong></p>',
                '</section>';
        }
       
    }
    
}

function affLivreL(array $livre) : void {
    // Le nom de l'auteur doit être encodé avec urlencode() avant d'être placé dans une URL, sans être passé auparavant par htmlentities()
    $auteurs = explode('@', $livre['auteurs']);
    unset($livre['auteurs']);
    $livre['edWeb'] = trim($livre['edWeb']);
    $livre = htmlProtegerSorties($livre);
    echo
        '<article class="arRecherche">',
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
            'ISBN13 : ', $livre['liISBN13'],'<br>',
            'Quantité : ',$livre['ccQuantite'],
        '</article>';
}

?>
