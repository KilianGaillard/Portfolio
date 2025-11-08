<?php

require_once './bibli_generale.php';
require_once './bibli_bookshop.php';
affDebutEnseigneEntete('BookShop | Inscription', false);



function traitementInscriptionL(){
    //indice du nombre d'erreurs
    $errors = [];
    $donnees = $_POST;

    if(empty($donnees['nom'])||!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $donnees['nom'])){
        $errors[] = "Le nom n'est pas valide.";
    }
    if(empty($donnees['prenom'])||!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $donnees['prenom'])){
        $errors[] = "Le prenom n'est pas valide.";
    }
    if($donnees['nom']!=htmlspecialchars($donnees['nom'])){
        $errors[] = "Le nom ne doit pas contenir de tags HTML.";
    }
    if($donnees['prenom']!=htmlspecialchars($donnees['prenom'])){
        $errors[] = "Le prenom ne doit pas contenir de tags HTML.";
    }

    if(empty($donnees['telephone'])){
        $errors[] = "Le numéro de téléphone ne doit pas être vide.";
    }
    else if(!preg_match('/^(\d{2}\.){4}\d{2}$/', $donnees['telephone'])){
        $errors[] = "Le numéro de téléphone n'est pas valide.";
    }
    if(empty($donnees['naissance'])){
        $errors[] = "Une date de naissance doit être soumise.";
    }else {
        list($annee, $mois, $jour) = explode('-', $donnees['naissance']);
        if(!checkdate($mois,$jour,$annee)){
            header("Location: ../index.php");
            exit();
        }
        else{
            $date_actuelle = new DateTime();
            $date_naissance = new DateTime($annee . '-' . $mois . '-' . $jour);
            $age = $date_actuelle->diff($date_naissance)->y;
            if ($age < 15) {
                $errors[] = "Vous devez avoir au moins 15 ans pour vous inscrire.";
            }
        }
    }
    if(strlen($donnees['pass1'])<4){
        $errors[]= "Le mot de passe doit contenir au moins 4 caractères";
    }
    if($donnees['pass1']!=$donnees['pass2']){
        $errors[] = "Les deux mots de passes doivent être identiques";
    }

    if(!isset($donnees['email'])){
        header("Location: ../index.php");
        exit();

    }
    if (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse e-mail n'est pas valide.";
    }
    
    
    if(!parametresControle('post',['nom','prenom','email','telephone','cbCGU','naissance','pass1','pass2','btnInscription'])){
        if(!isset($donnees['cbCGU'])){
            $errors[] = "Les conditions générales d'utilisation doivent être acceptées.";
        }else{
            header("Location: ../index.php");
            exit();
        }
        
    }
    $bd = bdConnect();
    $email = $donnees['email'];
    $sql = "SELECT COUNT(*) AS count FROM client WHERE cliEmail = '$email'";
    $res = mysqli_query($bd, $sql);
    
    if (!$res) {
        echo 'Erreur avec la requête SQL de récupération de l\'email éventuellement identique. ';
        mysqli_close($bd);
    }
    $row = mysqli_fetch_assoc($res);
    if($row['count']>0){
        $errors[] = "L'adresse email entrée est déjà associée à un autre compte.";
    }
    mysqli_free_result($res);
    
    //si aucune erreur détectée
    if(empty($errors)){
        

        $nom = mysqli_real_escape_string($bd, $donnees['nom']);
        $prenom = mysqli_real_escape_string($bd, $donnees['prenom']);
        $email = mysqli_real_escape_string($bd, $donnees['email']);
        $telephone = mysqli_real_escape_string($bd, $donnees['telephone']);
        $dateNaissance = DateTime::createFromFormat('Y-m-d', $donnees['naissance']);
        $naissance = $dateNaissance->format('dmY');
        $password = password_hash($donnees['pass1'], PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO client (cliNom, cliPrenom, cliEmail, cliTelephone, cliDateNaissance, cliPassword)
            VALUES ('$nom', '$prenom', '$email', '$telephone', '$naissance', '$password')
        ";

        if (mysqli_query($bd, $sql)) {
            echo '<p>Un nouvel utilisateur a été ajouté dans la table "client" de la base de données.</p>';
        } else {
            echo '<p>Erreur lors de l\'inscription : ' . mysqli_error($bd) . '</p>';
        }

        mysqli_close($bd);
        header("Location: ./protegee.php");
        exit();

    }else {
        return $errors;
    }
}


function affFormulaireL($errors,$donnees) {
        
        if (!empty($errors)) {
            echo '<div class="square_error">';
            echo '<div class="first_line_error">Les erreurs suivantes ont été relevées :</div>';
            foreach ($errors as $error) {
                echo '- ' .$error . '<br>';
            }
            echo '</div>';
        }


        echo 
    
        '<form method="post" action="../php/inscription_4.php">',
    
            '<section>',
                
                '<h2>Informations personnelles</h2>',
                    
                '<p>Merci de remplir les informations suivantes (tous les champs sont obligatoires) </p>',
                        
                    '<table>',
                        '<tr>',
                            '<td>Votre nom :</td>',
                            '<td><input type="text" name="nom" value="',isset($donnees['nom']) ? $donnees['nom'] : "",'" ></td>',
                        '</tr>',
                        '<tr>',
                            '<td>Votre prénom :</td>',
                            '<td><input type="text" name="prenom" value="',isset($donnees['prenom']) ? $donnees['prenom'] : "",'"></td>',
                        '</tr>',
                        '<tr>',
                            '<td>Votre adresse email :</td>',    
                            '<td><input type="email" name="email" value="',isset($donnees['email']) ? $donnees['email'] : "",'" placeholder="xxx@yyy.zz" ></td>',    
                        '</tr>',
                        '<tr>',
                            '<td>Votre numéro de téléphone :</td>',
                            '<td><input type="tel" name="telephone" value="',isset($donnees['telephone']) ? $donnees['telephone'] : "",'" placeholder="Format : 03.81.66.64.52" pattern="^(\d{2}\.){4}\d{2}$" ></td>',
                        '</tr>',
                        '<tr>',
                            '<td>Votre date de naissance :</td>',
                            '<td><input type="date" name="naissance" value="',isset($donnees['naissance']) ? $donnees['naissance'] : "",'" ></td>',
                        '</tr>',
                        '<tr>',
                            '<td>Choisissez un mot de passe :</td>',
                            '<td><input type="password" name="pass1" value="" placeholder="4 caractères minimum" ></td>',
                        '</tr>',   
                        '<tr>',
                            '<td>Répétez le mot de passe :</td>',
                            '<td><input type="password" name="pass2" value="" ></td>',
                        '</tr>',
                    '</table>',
                
            '</section>',
    
            '<section>',
                '<h2>Validation</h2>',
    
                '<p>',
                    '<input type="checkbox" name="cbCGU" id="condUsage" value="1"><label for="condUsage" >J\'ai bien lu et j\'accepte les conditions générales d\'utilisation.</label>',
                '</p>',
    
                '<p>',
                    '<input type="submit" name="btnInscription" value="S\'inscrire">',
                    '<input type="reset" value="Réinitialiser">',
                '</p>',
            '</section>',
            
        '</form>';
        $errors=[];

        

}
$errors=[];
if(isset($_POST['btnInscription'])){
    $errors = traitementInscriptionL();
}
affFormulaireL($errors,$_POST);
affPiedFin();


