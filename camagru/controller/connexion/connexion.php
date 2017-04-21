<?php
require_once("model/shared/user_id_from_login.php");
require_once("model/shared/formkey.class.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$formKey = new formKey();
$formKey2 = new formKey2();

if ($user_id)
	header('Location: index.php');

require_once("view/connexion/connexion.php");

?>