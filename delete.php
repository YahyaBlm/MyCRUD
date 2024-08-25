<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

if (!$_SESSION['auth']) {
    header('Location:login.php');
}

if ($_SESSION['auth']['role_level'] < 100) { 
    header('Location:profile.php?id='.$_SESSION['auth']['id_user']);
}

$id = $_GET['id'];

$selectUser = $db->prepare('SELECT * FROM users
    NATURAL JOIN roles
    WHERE id_user = ?
');
$selectUser->execute([$id]);
$user = $selectUser->fetch(PDO::FETCH_OBJ);

$selectAllUsers = $db->prepare('SELECT * FROM users
NATURAL JOIN roles
');
$selectAllUsers->execute();
$return = count($selectAllUsers->fetchAll());

if (isset($_POST['yes'])) {
    $deleteUser = $db->prepare('DELETE FROM users
    WHERE id_user = ?
');
    $deleteUser->execute([$id]);
    echo "<script language='javascript'>
        document.location.replace('./index.php')
        </script>";

    //header('Location:index.php');
}

if (isset($_POST['no'])) {
    echo "<script language='javascript'>
        document.location.replace('./index.php')
        </script>";

    //header('Location:index.php');
}

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');

?>


</head>

<body>
    <div class="container">
        <h1>Voulez-vous supprimer <?php echo $user->user_firstname; ?> <?php echo $user->user_lastname ?> ?</h1>
        <form action="" method="POST">

            <?php
            if ($return > 1) {
            ?>

                <input class="btn btn-success" type="submit" name="yes" value="Oui">

                <input class="btn btn-danger" type="submit" name="no" value="Non">
            <?php
            } else {
            ?>
                <h2>Cette commande n'est pas valable.</h2>
                <a class="btn btn-dark" href="./index.php">Retour à la liste</a>
            <?php
            }
            ?>
        </form>
    </div>
</body>

</html>