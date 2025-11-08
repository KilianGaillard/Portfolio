<?php
/* ------------------------------------------------------------------------------
    Architecture de la page
    - étape 1 : vérifications diverses et traitement de la soumission
    - étape 2 : génération du code HTML de la page
------------------------------------------------------------------------------*/

// chargement des bibliothèques de fonctions
require_once('bibli_bookshop.php');
require_once('bibli_generale.php');

// bufferisation des sorties
ob_start();

// démarrage ou reprise de la session
session_start();

/*------------------------- Etape 1 --------------------------------------------
- vérifications diverses et traitement de la soumission
------------------------------------------------------------------------------*/

// si l'utilisateur est déjà authentifié
if (estAuthentifie()){
    header ('Location: ../index.php');
    exit();
}

// si formulaire soumis, traitement de la demande d'inscription
if (isset($_POST['btnInscription'])) {
    $erreurs = traitementInscriptionL(); // ne retourne pas quand les données soumises sont valides
}
else{
    $erreurs = null;
}

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/

affDebutEnseigneEntete('BookShop | Inscription');

affContenuL($erreurs);

affPiedFin();

// fin du script --> envoi de la page
ob_end_flush(); // appel facultatif



/*********************************************************
 *
 * Définitions des fonctions locales de la page
 *
 *********************************************************/
// ----------  Fonctions locales du script ----------- //

/**
 * Contenu de la page : affichage du formulaire d'inscription
 *
 * En absence de soumission (i.e. lors du premier affichage), $errs est égal à null
 * Quand l'inscription échoue, $errs est un tableau de chaînes
 *
 * @param ?array    $errs   Tableau contenant les erreurs en cas de soumission du formulaire, null lors du premier affichage
 *
 * @return void
 */
function affContenuL(?array $errs) : void {
    // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
    if (isset($_POST['btnInscription'])){
        // la suppression des 2 éléments suivants est facultative, ils ne sont pas utilisés dans la suite de la fonction
        unset($_POST['pass1']);
        unset($_POST['pass2']);
        foreach($_POST as &$val){
            $val = trim($val);
        }
        unset($val); // à ne pas oublier !

        // la protection des sorties avec htmlProtegerSorties() est réalisée au dernier moment
        // juste avant l'encapsulation des données soumises dans le code HTML
        $values = htmlProtegerSorties($_POST);
    }
    else{
        $values['nom'] = $values['prenom'] = $values['email'] = $values['telephone'] = $values['naissance'] = '';
    }


    echo
        '<form method="post" action="', basename($_SERVER['PHP_SELF']), '">',
            '<section>';

    if (is_array($errs)){
        echo    '<p class="error"><strong>Les erreurs suivantes ont été relevées lors de votre inscription :</strong>';
        foreach ($errs as $e) {
            echo        '<br>- ', $e;
        }
        echo    '</p>';
    }

    echo
                '<h2>Informations personnelles</h2>',
                '<p>Merci de remplir les informations suivantes (tous les champs sont obligatoires) </p>',
                '<table>',
                    '<tr>',
                        '<td>Votre nom :</td>',
                        '<td><input type="text" name="nom" value="', $values['nom'], '" required></td>',
                    '</tr>',
                    '<tr>',
                        '<td>Votre prenom :</td>',
                        '<td><input type="text" name="prenom" value="', $values['prenom'], '" required></td>',
                    '</tr>',
                    '<tr>',
                        '<td>Votre adresse email :</td>',
                        '<td><input type="email" name="email" value="', $values['email'], '" placeholder="xxx@yyy.zz" required></td>',
                    '</tr>',
                    '<tr>',
                        '<td>Votre numéro de téléphone :</td>',
                        '<td>',
                            '<input type="tel" name="telephone" value="', $values['telephone'],
                            '" placeholder="Format : 03.81.66.64.52" pattern="^(\d{2}\.){4}\d{2}$" required>',
                        '</td>',
                    '</tr>',
                    '<tr>',
                        '<td>Votre date de naissance :</td>',
                        '<td><input type="date" name="naissance" value="', $values['naissance'], '" required></td>',
                    '</tr>',
                    '<tr>',
                        '<td>Choisissez un mot de passe :</td>',
                        '<td>',
                            '<input type="password" name="pass1" value="" placeholder="', LMIN_PASSWORD, ' caractères minimum" required>',
                        '</td>',
                    '</tr>',
                    '<tr>',
                        '<td>Répétez le mot de passe :</td>',
                        '<td><input type="password" name="pass2" value="" required></td>',
                    '</tr>',
                '</table>',
            '</section>',
            '<section>',
                '<h2>Validation</h2>',
                '<p>',
                    '<input type="checkbox" name="cbCGU" id="condUsage" value="1" required>',
                    '<label for="condUsage">J\'ai bien lu et j\'accepte les conditions générales d\'utilisation.</label>',
                '</p>',
                '<p>',
                    '<input type="submit" name="btnInscription" value="S\'inscrire">',
                    '<input type="reset" value="Réinitialiser">',
                '</p>',
            '</section>',
        '</form>';
}


/**
 * Traitement d'une demande d'inscription
 *
 * Vérification de la validité des données
 *
 * Les erreurs de type "étourderie" sont stockées dans un tableau php qui est retourné par la fonction.
 *
 * Toutes les erreurs détectées qui nécessitent une modification du code HTML du formulaire sont considérées comme des tentatives de piratage
 * et donc entraînent une redirection de l'utilisateur vers la page index.php sauf :
 * - les éventuelles suppressions des attributs pattern et required car ces attributs sont des nouveautés apparues dans la version HTML5 et
 *   nous souhaitons que l'application fonctionne également correctement sur les vieux navigateurs qui ne supportent pas encore HTML5
 * - une éventuelle modification de l'input de type date en input de type text car c'est ce que font les navigateurs qui ne supportent
 *   pas les input de type date
 *
 * Si les données soumises sont valides, un nouvel utilisateur est ajouté dans la table utilisateur.
 *
 *  @return array    un tableau contenant les erreurs s'il y en a
 */
function traitementInscriptionL(): array {
    if( !parametresControle('post', ['nom', 'prenom', 'email', 'telephone', 'naissance',
                                     'pass1', 'pass2', 'btnInscription'], ['cbCGU'])) {
        sessionExit();
    }

    $erreurs = [];

    // vérification des noms et prénoms
    $expRegNomPrenom = '/^[[:alpha:]]([\' -]?[[:alpha:]]+)*$/u';
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    verifierTexte($nom, 'Le nom', $erreurs, LMAX_NOM, $expRegNomPrenom);
    verifierTexte($prenom, 'Le prénom', $erreurs, LMAX_PRENOM, $expRegNomPrenom);

    // vérification du format de l'adresse email
    $email = trim($_POST['email']);
    verifierTexte($email, 'L\'adresse email', $erreurs, LMAX_EMAIL);

    // la validation faite par le navigateur en utilisant le type email pour l'élément HTML input
    // est moins forte que celle faite ci-dessous avec la fonction filter_var()
    // Exemple : 'l@i' passe la validation faite par le navigateur et ne passe pas
    // celle faite ci-dessous
    if(! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'L\'adresse email n\'est pas valide.';
    }

    // vérification du numéro de téléphone
    $tel = trim($_POST['telephone']);
    verifierTexte($tel, 'Le numéro de téléphone', $erreurs, LMAX_TELEPHONE, '/^(\\d{2}\\.){4}\\d{2}$/u');

    // vérification de la date de naissance
    if (empty($_POST['naissance'])){
        $erreurs[] = 'La date de naissance doit être renseignée.';
    }
    else{
        if(! preg_match('/^\\d{4}(-\\d{2}){2}$/u', $_POST['naissance'])){ //vieux navigateur qui ne supporte pas le type date ?
            $erreurs[] = 'la date de naissance doit être au format "AAAA-MM-JJ".';
        }
        else{
            list($annee, $mois, $jour) = explode('-', $_POST['naissance']);
            if (!checkdate($mois, $jour, $annee)) {
                $erreurs[] = 'La date de naissance n\'est pas valide.';
            }
            else if (mktime(0,0,0,$mois,$jour,$annee + AGE_MINIMUM) > time()) {
                $erreurs[] = 'Vous devez avoir au moins '. AGE_MINIMUM. ' ans pour vous inscrire.';
            }
        }
    }

    // vérification des mots de passe
    if ($_POST['pass1'] !== $_POST['pass2']) {
        $erreurs[] = 'Les mots de passe doivent être identiques.';
    }
    $nb = mb_strlen($_POST['pass1'], encoding:'UTF-8');
    if ($nb < LMIN_PASSWORD){
        $erreurs[] = 'Le mot de passe doit être constitué d\'au moins '. LMIN_PASSWORD . ' caractères.';
    }

    // vérification de la valeur de l'élément cbCGU
    if (! isset($_POST['cbCGU'])){
        $erreurs[] = 'Vous devez accepter les conditions générales d\'utilisation .';
    }
    else if ($_POST['cbCGU'] !== '1'){
        sessionExit();
    }

    // si erreurs --> retour
    if (count($erreurs) > 0) {
        return $erreurs;   //===> FIN DE LA FONCTION
    }

    // on vérifie si l'adresse email n'est pas déjà utilisée que si tous les autres champs
    // sont valides car cette dernière vérification nécessite une connexion au serveur de base de données
    // consommatrice de ressources système

    // ouverture de la connexion à la base
    $bd = bdConnect();

    // la protection des entrées avec mysqli_real_escape_string() est réalisée au dernier moment
    // juste avant le placement de l'email dans la requête SQL
    $email = mysqli_real_escape_string($bd, $email);

    $sql = "SELECT cliID FROM client WHERE cliEmail = '$email'";
    $res = bdSendRequest($bd, $sql);

    $tab = mysqli_fetch_assoc($res); // autre possibilité : utiliser la fonction mysqli_num_rows()
    if ($tab != null){
        $erreurs[] = 'L\'adresse email est déjà utilisée.';
    }
    // Libération de la mémoire associée au résultat de la requête
    mysqli_free_result($res);

    // si erreurs --> retour
    if (count($erreurs) > 0) {
        // fermeture de la connexion à la base de données
        mysqli_close($bd);
        return $erreurs;   //===> FIN DE LA FONCTION
    }

    // calcul du hash du mot de passe pour enregistrement dans la base.
    $pass = password_hash($_POST['pass1'], PASSWORD_DEFAULT);

    // la protection des entrées avec mysqli_real_escape_string() est réalisée au dernier moment
    // juste avant placement des chaînes saisies dans la requête SQL

    $pass = mysqli_real_escape_string($bd, $pass);

    $dateNaissance = $annee*10000 + $mois*100 + $jour;

    $nom = mysqli_real_escape_string($bd, $nom);
    $prenom = mysqli_real_escape_string($bd, $prenom);
    $tel = mysqli_real_escape_string($bd, $tel);

    $sql = "INSERT INTO client (cliEmail, cliTelephone, cliPassword, cliPrenom, cliNom, cliDateNaissance)
            VALUES ('$email', '$tel', '$pass', '$prenom', '$nom', $dateNaissance)";

    bdSendRequest($bd, $sql);

    $_SESSION['cliID'] = mysqli_insert_id($bd);

    // fermeture de la connexion à la base de données
    mysqli_close($bd);

    header('Location: protegee.php');
    exit(); //===> Fin du script
}
