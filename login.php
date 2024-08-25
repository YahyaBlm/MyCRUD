<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

if (isset($_SESSION['auth']) && $_SESSION['auth']['role_level'] < 50) { 
    header('Location:profile.php?id='.$_SESSION['auth']['id_user']);
}

$user = NULL;

if (isset($_POST['login'])) {
    if (!empty($_POST['user_mail']) && !empty($_POST['user_mdp'])) {
        $req = $db->prepare('SELECT * FROM users
        NATURAL JOIN roles
        WHERE user_mail = ?
        ');
        $req->execute([$_POST['user_mail']]);
        $user = $req->fetch();
    }

    if ($user) {
        if (password_verify($_POST['user_mdp'], $user['user_mdp'])) {

            $_SESSION['auth'] = $user;

            if ($_SESSION['auth']['role_level'] < 50) {

                header('Location:profile.php?id=' . $_SESSION['auth']['id_user']);
            } else {
                echo "<script language='javascript'>
                document.location.replace('./index.php')
                </script>";

                //header('Location:index.php');
            }
        }
    }
}

if (isset($_POST['signin'])) {
    header('Location:inscription.php');
}

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');

?>

</head>

<body>
    <div class="container">

        <h1>Connectez-vous !</h1>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="mail" class="form-label">Email</label>
                <input type="email" class="form-control" id="mail" placeholder="Email" name="user_mail">
            </div>

            <div class="mb-3">
                <label for="mdp" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mdp" placeholder="Mot de passe" name="user_mdp">
            </div>

            <input class="btn btn-primary" type="submit" name="login" value="Se conecter">
            <input class="btn btn-primary" type="submit" name="signin" value="S'inscrire">
        </form>
    </div>

</body>

</html>