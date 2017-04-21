<?php
require_once("../../config/setup.php");
require_once("../../model/connexion/login.php");

if ($user_id = user_id_from_login($_SESSION['loggued_on_user']))
{
	header('Location: ../../index.php');
	exit;
}

$formKey = new formKey();

if (isset($_POST['check_login']) && isset($_POST['check_passwd'])
    && $_POST['check_login'] !== "" && $_POST['check_passwd'] !== "" && $_POST['check_submit'] === 'OK' && isset($_POST['form_key']) && $formKey->validate())
{
	$login = htmlspecialchars($_POST["check_login"]);
	$pwd = htmlspecialchars($_POST["check_passwd"]);
	$user = auth($login, $pwd);
	if ($user)
	{
		if ($user[2] !== 1)
			header('Location: ../../connexion.php?error_confirmation=1');
		else
		{
			$_SESSION['loggued_on_user'] = $login;
			header('Location: ../../index.php');
		}
	}
	else
		header('Location: ../../connexion.php?error_connect=1');
}
else
	header('Location: ../../connexion.php?verif_error=1');
?>