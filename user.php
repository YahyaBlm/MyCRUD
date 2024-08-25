<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

if (!$_SESSION['auth']) {
    header('Location:login.php');
}

if ($_SESSION['auth']['role_level'] < 50) { 
    header('Location:profile.php?id='.$_SESSION['auth']['id_user']);
}

$id = $_GET['id'];

$selectUser = $db->prepare('SELECT * FROM users
    NATURAL JOIN roles
    WHERE id_user = ?
');
$selectUser->execute([$id]);
$user = $selectUser->fetch(PDO::FETCH_OBJ);

$id_role = $user->id_role;
$mail = $user->user_mail;

$selectAllRoles = $db->prepare('SELECT * FROM roles
WHERE id_role != ?
ORDER BY role_level ASC
');
$selectAllRoles->execute([$id_role]);

//fonctionalité metier de modification de l'utilisateur

if (isset($_POST['updateUser'])) {
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
    if (empty($errors)) {
        $updateUser = $db->prepare('UPDATE users SET
        user_firstname = ?,
        user_lastname = ?,
        user_mail = ?,
        id_role = ?
        WHERE id_user = ?
        ');

        $updateUser->execute([
            $_POST['user_firstname'],
            $_POST['user_lastname'],
            $_POST['user_mail'],
            $_POST['id_role'],
            $id
        ]);

        echo "<script language='javascript'>
        document.location.replace('./index.php')
        </script>";

        //header('Location:index.php');
    }
}

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');

?>

</head>

<body>

    <div class="container">

        <H1>Vue de l'utilisateur</H1>

        <a class="btn btn-dark" href="./index.php">Retour à la liste</a>

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
                <label for="id_role">Rôle de l'utilisateur</label>
                <select class="form-select" id="id_role" name="id_role" aria-label="Default select example">
                    <option value="<?php echo $user->id_role; ?>" selected><?php echo ucfirst($user->role_name); ?></option>
                    <?php
                    while ($sAR = $selectAllRoles->fetch(PDO::FETCH_OBJ)) {
                    ?>
                        <option value="<?php echo $sAR->id_role; ?>"><?php echo ucfirst($sAR->role_name); ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div>
                <?php if ($_SESSION['auth']['role_level'] > 49) { ?>
                <input class="btn btn-success" type="submit" name="updateUser" Value="Modifier">
                <?php } ?>
            </div>
        </form>
    </div>

</body>

</html>