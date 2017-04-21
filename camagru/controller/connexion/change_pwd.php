<?php

require_once("model/shared/user_id_from_login.php");
require_once("model/shared/formkey.Class.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$formKey = new formKey();

if ($user_id)
{
    header('Location: index.php');
    exit;
}

require_once("view/connexion/change_pwd.php");

?>