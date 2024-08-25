<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');

$id = $_GET['id'];

if($id != $_SESSION['auth']['id_user']){
    header('Location:'.$_SERVER['PHP_SELF'].'?id='.$_SESSION['auth']['id_user']);
}

$selectUser = $db->prepare('SELECT * FROM users
    NATURAL JOIN roles
    WHERE id_user = ?
');
$selectUser->execute([$id]);
$user = $selectUser->fetch(PDO::FETCH_OBJ);

if (isset($_POST['editUser'])) {
    $errors = array();

    if (empty($_POST['user_firstname']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['user_firstname'])) {
        $errors['user_firstname'] = "Le champs 'Prénom' n'est pas valide";
    }

    if (empty($_POST['user_lastname']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['user_lastname'])) {
        $errors['user_lastname'] = "Le champs 'Nom' n'est pas valide";
    }

    if (empty($_POST['user_mail']) || !filter_var($_POST['user_mail'], FILTER_VALIDATE_EMAIL)) {
        $errors['user_mail'] = "Il ne s'agit pas d'un mail";
    } else {
        $req = $db->prepare('SELECT id_user from users 
        WHERE user_mail = ?
        ');
        $req->execute([$_POST['user_mail']]);
        $email = $req->fetch(PDO::FETCH_OBJ);
        if ($email) {
            if ($email->id_user != $id) {
                $errors['user_mail'] = 'Un compte existe déja pour cet Email.';
            }
        }
    }

    if (!empty($_POST['user_mdp'])) {
        if (password_verify($_POST['user_mdp'], $user->user_mdp)) {
            if (empty($_POST['new_mdp']) || $_POST['new_mdp'] != $_POST['confMdp']) {
                $errors['new_mdp'] = 'Vos nouveaux mots de passe sont vides ou non identiques';
            } else {
                $password = password_hash($_POST['new_mdp'], PASSWORD_BCRYPT);
            }
        } else {
            $errors['user_mdp'] = 'Votre ancien mot de passe est incorrect';
        }
    } else {
        $password = $user->user_mdp;
    }

    if (isset($_FILES['fileUpload']) && is_uploaded_file($_FILES['fileUpload']['tmp_name'][0])) {
        $destinationDir = 'imgs_profiles/' . $id ;
        $destinationFile = $destinationDir . '/' . basename($_FILES['fileUpload']['name'][0]);

        // Crée le dossier utilisateur s'il n'existe pas
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        // Déplace le fichier uploadé vers le répertoire de destination
        if (move_uploaded_file($_FILES['fileUpload']['tmp_name'][0], $destinationFile)) {
            // Met à jour le chemin de l'image dans la base de données
            $updateImg = $db->prepare('UPDATE users SET user_img = ? WHERE id_user = ?');
            $updateImg->execute([$destinationFile, $id]);
        } else {
            $errors['fileUpload'] = "Erreur lors du déplacement du fichier.";
        }
    }

    if (empty($errors)) {
        $updateUser = $db->prepare('UPDATE users SET
            user_firstname = ?,
            user_lastname = ?,
            user_mail = ?,
            user_mdp = ?,
            user_img = ?
            WHERE id_user = ?
        ');

        $updateUser->execute([
            $_POST['user_firstname'],
            $_POST['user_lastname'],
            $_POST['user_mail'],
            $password,
            $_FILES['fileUpload']['name'][0],
            $id
        ]);

        header('Location: profile.php?id=' . $id);
        exit();
    }
}

?>

</head>

<body>

    <div class="container">

        <H1>Vue de l'utilisateur</H1>

        <a class="btn btn-dark" href="./profile.php?id=<?php echo $_SESSION['auth']['id_user'] ?>">Retour à mon profile</a>

        <?php
        if (!empty($errors)) {
        ?>

            <div id="zoneErreur">
                <div id="danger" class="alert alert-danger" role="alert">
                    <p>Le formulaire n'est pas correctement renseigné :</p>
                    <ul>
                        <?php
                        foreach ($errors as $error) {
                        ?>
                            <li><?php echo $error; ?></li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        <?php
        }
        ?>

        <form action="" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="firstname" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="firstname" placeholder="Prénom" name="user_firstname" value="<?php echo $user->user_firstname; ?>">
            </div>

            <div class="mb-3">
                <label for="lastname" class="form-label">Nom</label>
                <input type="text" class="form-control" id="lastname" placeholder="Nom" name="user_lastname" value="<?php echo $user->user_lastname; ?>">
            </div>

            <div class="mb-3">
                <label for="mail" class="form-label">Email</label>
                <input type="email" class="form-control" id="mail" placeholder="Email" name="user_mail" value="<?php echo $user->user_mail; ?>">
            </div>

            <div class="mb-3">
                <label for="mdp" class="form-label">Ancien mot de passe</label>
                <input type="password" class="form-control" id="mdp" placeholder="Ancien mot de passe" name="user_mdp">
            </div>

            <div class="mb-3">
                <label for="new_mdp" class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" id="new_mdp" placeholder="Mot de passe" name="new_mdp">
            </div>

            <div class="mb-3">
                <label for="confMdp" class="form-label">Confirmer mot de passe</label>
                <input type="password" class="form-control" id="confMdp" placeholder="Confirmer mot de passe" name="confMdp">
            </div>

            <div class="mb-3">
                <label for="fileUpload" class="form-label">Photo de profile</label>
                <input type="file" class="form-control" id="fileUpload" name="fileUpload[]">
            </div>

            <div>
                <input class="btn btn-success" type="submit" name="editUser" Value="Modifier">
            </div>
        </form>
    </div>

</body>

</html>