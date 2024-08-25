<?php

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/host.php');

$id = $_GET['id'];
if (isset($_POST['yes'])) {
    var_dump($_SESSION);
    if ($_SESSION['auth']['id_user'] == $id) {
        $deleteMe = $db->prepare('DELETE FROM users
        WHERE id_user = ?
    ');
        $deleteMe->execute([$id]);
        unset($_SESSION['auth']);
        
        // echo "<script language='javascript'>
        // document.location.replace('./login.php')
        // </script>";

        header('Location:login.php');
    }
}

if (isset($_POST['no'])) {
    // echo "<script language='javascript'>
    //     document.location.replace('./profile.php')
    //     </script>";

    header('Location:profile.php?id=' . $_SESSION['auth']['id_user']);
}

include($_SERVER['DOCUMENT_ROOT'] . '/MyCRUD/_blocks/doctype.php');

?>

</head>

<body>
    <div class="container">
        <h1>Voulez-vous vous déinscrire ?</h1>
        <form action="" method="POST">

            <input class="btn btn-success" type="submit" name="yes" value="Oui">

            <input class="btn btn-danger" type="submit" name="no" value="Non">

        </form>
    </div>
</body>

</html>