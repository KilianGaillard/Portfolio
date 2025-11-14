<?php
require_once 'bibli_bookshop.php';
require_once('bibli_generale.php');

ob_start();
session_start();

// sauvegarder la page précédente
if (!isset($_SESSION['page_precedente']) && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['page_precedente'] = $_SERVER['HTTP_REFERER'];
}

// si l'utilisateur est déjà authentifié
if (estAuthentifie()){
    // Redirection vers la page précédente s'il existe
    if (isset($_SESSION['page_precedente'])) {
        $url = $_SESSION['page_precedente'];
        unset($_SESSION['page_precedente']); // nettoie après utilisation
        header('Location: ' . $url);
    } else {
        header('Location: ../index.php');
    }
    exit();
}

// traitement du formulaire
if (isset($_POST['btnConnexion'])) {
    $erreurs = traitementConnexionL();
} else {
    $erreurs = null;
}

affDebutEnseigneEntete('BookShop | Connexion');
affConnexion($erreurs);
affPiedFin();




function affConnexion(?array $errs){

    // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
    if (isset($_POST['btnConnexion'])){
        // la suppression des 2 éléments suivants est facultative, ils ne sont pas utilisés dans la suite de la fonction
        unset($_POST['pass1']);
        foreach($_POST as &$val){//on place $val en tant que référence aux valeurs du formulaire
            $val = trim($val);
        }
        unset($val); // à ne pas oublier afin de retirer les références, et éviter des bugs

        // la protection des sorties avec htmlProtegerSorties() est réalisée au dernier moment
        // juste avant l'encapsulation des données soumises dans le code HTML
        $values = htmlProtegerSorties($_POST);
    }
    else{
        $values['email'] = '';
    }




    echo
    '<form method="post" action="', basename($_SERVER['PHP_SELF']), '">',
        '<section>';

    if (is_array($errs)){
    echo    '<p class="error"><strong>Les erreurs suivantes ont été relevées lors de votre connexion :</strong>';
    foreach ($errs as $e) {
        echo        '<br>- ', $e;
    }
    echo    '</p>';
    }

    echo
                    '<h2>Connectez-vous</h2>',
                    '<p>Merci de remplir les informations suivantes (tous les champs sont obligatoires) </p>',
                    '<table>';
                    affLigneInput('Votre adresse email :', array('type' => 'email', 'name' => 'email', 'value' => $values['email'],
                    'placeholder'=>'xxx@yyy.zz', 'required' => null));
                    affLigneInput('Votre mot de passe :', array('type' => 'password', 'name' => 'pass1', 'value' => '',
                    'placeholder' => LMIN_PASSWORD . ' caractères minimum', 'required' => null));
                    echo
                    '</table>',
                '</section>',
                '<div>',
                    '<p class="boutonàaligner">',
                        '<input type="submit" class="btn-inscription" name="btnConnexion" value="Valider">',
                        '<input type="reset" class="btn-inscription" value="Réinitialiser">',
                    '</p>',
                '</div>',
            '</form>';

            echo '<h2>Inscription</h2>',
            '<p>Vous n\'avez pas de compte ? Inscrivez-vous !</p>',
            '<div>',
                '<p class="boutonàaligner">',
                    '<button class="btn-inscription" onclick="window.location.href=\'inscription.php\';">',
                        'S\'inscrire',
                    '</button>',
                '</p>',
            '</div>';
    

}

function traitementConnexionL(){

    $erreurs = [];
    $bd = bdConnect();

    $email = trim($_POST['email']);
    $password = trim($_POST['pass1']);

    //vérification de l'email brut
    verifierTexte($email, 'L\'adresse email', $erreurs, LMAX_EMAIL);
    if(! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'L\'adresse email n\'est pas valide.';
    }

    //si pas d'erreurs, alors on protège pour SQL
    if (count($erreurs) > 0) {
        return $erreurs;
    }
    $email = mysqli_real_escape_string($bd, $email);
    $password = mysqli_real_escape_string($bd, $password);

    //requête de vérification de connexion

    $sql = mysqli_query($bd, "SELECT cliID FROM client WHERE cliEmail = '$email' AND cliPassword = '$password'");
    if (mysqli_num_rows($sql) == 1) {
        $row = mysqli_fetch_assoc($sql);
        $_SESSION['cliID'] = $row['cliID'];
    } else {
        $erreurs[] = 'Email ou mot de passe incorrect';
        mysqli_close($bd);
        return $erreurs;
    }

    //fermeture de la connexion à la base de données
    mysqli_free_result($sql);
    mysqli_close($bd);

    if (isset($_SESSION['page_precedente'])) {
        $url = $_SESSION['page_precedente'];
        unset($_SESSION['page_precedente']);
        header('Location: ' . $url);
    } else {
        header('Location: index.php');
    }
    exit();


}
