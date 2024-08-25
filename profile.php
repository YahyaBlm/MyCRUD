<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

if (!$_SESSION['auth']) {
    header('Location:login.php');
}

$id = $_GET['id'];

$selectUser = $db->prepare('SELECT * FROM users
    NATURAL JOIN roles
    WHERE id_user = ?
');
$selectUser->execute([$id]);
$user = $selectUser->fetch(PDO::FETCH_OBJ);

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');


?>

<link rel="stylesheet" href="./assets/styles.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a class="edit btn btn-success" href="./edit_user.php?id=<?php echo $_SESSION['auth']['id_user'] ?>">Modifier mon profil</a>

            <?php if ($_SESSION['auth']['role_level'] > 49) { ?>
                <a class="logout btn btn-dark" href="./index.php?" .$id>Administration</a>
            <?php } ?>
            <a class="logout btn btn-danger" href="./signout.php?id=<?php echo $user->id_user?>">Se désinscrire</a>

            <a class="logout btn btn-warning" href="./logout.php">Se déconnecter</a>
        </aside>
        <?php if ($user) { ?>
            <main class="content">
                <h1>Profil Utilisateur</h1>
                <p><strong>Prénom :</strong> <?php echo $user->user_firstname; ?></p>
                <p><strong>Nom :</strong> <?php echo $user->user_lastname; ?></p>
                <p><strong>Email :</strong> <?php echo $user->user_mail; ?></p>
            </main>

            <div class="profile-image">
                <img src="./imgs_profiles/<?php echo $id . '/' . $user->user_img; ?>" alt="Ma photo de profil">
            </div>
        <?php } else {
            echo '<main class="content"><h1>Profile inexistant</h1></main>';
        }
        ?>
    </div>
</body>

</html>