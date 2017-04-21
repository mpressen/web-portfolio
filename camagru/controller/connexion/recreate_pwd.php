<?php

require_once("model/shared/user_from_login_and_confirmkey.php");
require_once("model/shared/user_id_from_login.php");
require_once("model/shared/formkey.class.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$formKey = new formKey();
if ($user_id)
{
    header('Location: index.php');
	exit;
}

if (isset($_GET['login']) && isset($_GET['confirmkey']) && $_GET['login'] !== '' && $_GET['confirmkey'] !== '')
{
	$login = htmlspecialchars($_GET['login']);
	$confirmkey = htmlspecialchars($_GET['confirmkey']);
	
	if (!user_from_login_and_confirmkey($login, $confirmkey))
	{
		header('Location: connexion.php?verif_error=1');
		exit;
	}
	require_once("view/connexion/recreate_pwd.php");
}
else
	header('Location: connexion.php?verif_error=1');
	


?>