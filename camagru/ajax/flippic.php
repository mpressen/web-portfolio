<?php

require_once("../config/setup.php");
require_once("../model/play/flippic.php");

$picture_id = htmlspecialchars($_POST["picture_id"]);
$user_id = user_id_from_login($_SESSION['loggued_on_user']);

if ($picture_id && $user_id)
    toggle_flip_status($picture_id, $user_id);
else
    header('Location: ../connexion.php');

?>