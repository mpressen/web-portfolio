<?php

require_once("../config/setup.php");
require_once("../model/play/renamepic.php");

$picture_id = htmlspecialchars($_POST["picture_id"]);
$name = htmlspecialchars($_POST["name"]);
$user_id = user_id_from_login($_SESSION['loggued_on_user']);

if ($picture_id && $name && $user_id)
	update_pic_title($name, $user_id, $picture_id);
else
    header('Location: ../connexion.php');

?>
