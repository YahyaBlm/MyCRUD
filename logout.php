<?php

session_start();
unset($_SESSION['auth']);

echo "<script language='javascript'>
        document.location.replace('./login.php')
        </script>";

    //header('Location:login.php');

?>