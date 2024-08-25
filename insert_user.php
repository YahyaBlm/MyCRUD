<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

if (!$_SESSION['auth']) {
    header('Location:login.php');
}

if ($_SESSION['auth']['role_level'] < 100) { 
    header('Location:profile.php?id='.$_SESSION['auth']['id_user']);
}

$selectAllRoles = $db->prepare('SELECT * FROM roles
ORDER BY role_level ASC
');
$selectAllRoles->execute();

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');

//fonctionalité metier

if (isset($_POST['addUser'])) {
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
            $errors['user_mail'] = 'Un compte existe déja pour cet Email.';
        }
    }

    if (empty($_POST['user_mdp']) || $_POST['user_mdp'] != $_POST['confMdp']) {
        $errors['user_mdp'] = 'Vos mots de passes sont vides ou non identiques';
    }

    if (empty($errors)) {
        $insertUser = $db->prepare('INSERT INTO users SET
        user_firstname = ?,
        user_lastname = ?,
        user_mail = ?,
        id_role = ?,
        user_mdp = ?
        ');

        $password = password_hash($_POST['user_mdp'], PASSWORD_BCRYPT);
        $insertUser->execute([
            $_POST['user_firstname'],
            $_POST['user_lastname'],
            $_POST['user_mail'],
            $_POST['id_role'],
            $password
        ]);
        header('Location:./index.php');
    }
}

?>

</head>

<body>

    <div class="container">

        <h1>Insertion d'un nouvel utilisateur</h1>

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

        <form action="" method="POST">

            <div class="mb-3">
                <label for="firstname" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="firstname" placeholder="Prénom" name="user_firstname">
            </div>

            <div class="mb-3">
                <label for="lastname" class="form-label">Nom</label>
                <input type="text" class="form-control" id="lastname" placeholder="Nom" name="user_lastname">
            </div>

            <div class="mb-3">
                <label for="mail" class="form-label">Email</label>
                <input type="email" class="form-control" id="mail" placeholder="Email" name="user_mail">
            </div>

            <div class="mb-3">
                <label for="id_role">Rôle de l'utilisateur</label>
                <select class="form-select" id="id_role" name="id_role" aria-label="Default select example">
                    <?php
                    while ($sAR = $selectAllRoles->fetch(PDO::FETCH_OBJ)) {
                    ?>
                        <option value="<?php echo $sAR->id_role; ?>"><?php echo ucfirst($sAR->role_name); ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="mdp" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mdp" placeholder="Mot de passe" name="user_mdp">
            </div>

            <div class="mb-3">
                <label for="confMdp" class="form-label">Confirmer mot de passe</label>
                <input type="password" class="form-control" id="confMdp" placeholder="Confirmer mot de passe" name="confMdp">
            </div>

            <input class="btn btn-dark" type="submit" name="addUser" Value="Ajouter">

        </form>
    </div>

</body>

</html>