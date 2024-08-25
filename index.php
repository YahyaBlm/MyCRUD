<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

$selectAllUsers = $db->prepare('SELECT * FROM users
NATURAL JOIN roles
');
$selectAllUsers->execute();

if ($_SESSION['auth']['role_level'] < 50) { 
    header('Location:profile.php?id='.$_SESSION['auth']['id_user']);
}

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');

?>

</head>

<body>

    <?php
    if (isset($_SESSION['auth'])) {
    ?>

        <div class="container">
            <div class="d-flex justify-content-between">
                <h1>Liste des utilisateurs</h1>

                <a class="btn btn-dark" href="./profile.php?id=<?php echo $_SESSION['auth']['id_user'] ?>">Mon Profile</a>
            </div>

            <table class="table table-light table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Prénom</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Role</th>
                        <th scope="col">Date de création</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($sAU = $selectAllUsers->fetch(PDO::FETCH_OBJ)) {
                    ?>
                        <tr>
                            <th scope="row"><?php echo $sAU->id_user; ?></th>
                            <td><?php echo ucfirst($sAU->user_firstname); ?></td>
                            <td><?php echo strtoupper($sAU->user_lastname); ?></td>
                            <td><?php echo ucfirst($sAU->role_name); ?></td>
                            <td><?php echo $sAU->user_insert_date; ?></td>
                            <td><!--call to action-->
                                <?php if ($_SESSION['auth']['role_level'] > 49) { ?>
                                    <a class="btn btn-success" href="./user.php?id=<?php echo $sAU->id_user ?>">Modifier</a>
                                <?php } else { ?>
                                    <a class="btn btn-primary" href="./user.php?id=<?php echo $sAU->id_user ?>">Voir</a>
                                <?php }
                                if ($_SESSION['auth']['role_level'] > 99) { ?>
                                    <a class="btn btn-danger" href="./delete.php?id=<?php echo $sAU->id_user ?>">Supprimer</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between">
                <?php if ($_SESSION['auth']['role_level'] > 99) { ?>
                    <a class="btn btn-dark" href="./insert_user.php">Nouvel utilisateur</a>
                <?php } ?>

                <a class="btn btn-warning" href="./logout.php">Se déconnecter</a>
            </div>
        </div>

    <?php
    } else {
        echo "<script language='javascript'>
        document.location.replace('./logout.php')
        </script>";

        //header('Location:logout.php');
    }
    ?>
</body>

</html>