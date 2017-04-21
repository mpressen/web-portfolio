<?php

require_once("../../config/setup.php");
require_once("../../model/connexion/create_user.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$formKey2 = new formKey2();

if (!$user_id && isset($_POST['create_login']) && isset($_POST['create_passwd']) && isset($_POST['create_email']) && isset($_POST['create_submit'])
    && $_POST['create_login'] !== "" && $_POST['create_passwd'] !== "" && $_POST['create_email'] !== "" && $_POST['create_submit'] === 'OK' && isset($_POST['form_key2']) && $formKey2->validate())
{
	$login = htmlspecialchars($_POST["create_login"]);
	if (!preg_match("/^[a-zA-Z ]*$/", $login))
	{
		header('Location: ../../connexion.php?name_err=1');
		exit;
	}
	
	if (login_already_in_database($login))
	{
		header('Location: ../../connexion.php?error_login=1');
		exit;
	}
	
	$email = htmlspecialchars($_POST["create_email"]);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		header('Location: ../../connexion.php?email_invalid=1');
		exit;
	}
	
	if (mail_already_in_database($email))
	{
		header('Location: ../../connexion.php?error_email=1');
		exit;
	}

    // check if password is valid
	$pwd = htmlspecialchars($_POST["create_passwd"]);
	if (strlen($pwd) < 5 || strlen($pwd) > 10 || !preg_match("#[0-9]+#", $pwd) || !preg_match("#[A-Z]+#", $pwd))
	{
		header('Location: ../../connexion.php?pwd_invalid=1');
		exit;
	}
	
	$key = create_new_user($login, $pwd, $email);
	
	//send confirmation mail
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $email))
		$passage_ligne = "\r\n";
	else
		$passage_ligne = "\n";
	$path = $_SERVER['HTTP_HOST'].dirname(htmlspecialchars($_SERVER['PHP_SELF']));
	$message_txt = "Hello ".$_POST['create_login'].", click on this link to activate your account!";
	$message_html = "<html><head></head><body><p>Hello ".$_POST['create_login'].", click on this link to activate your account!<br><a href='http://".$path."/verif.php?login=".$_POST['create_login']."&confirmkey=".$key."'>Activate your account</a></body></html>";
	$boundary = "-----=".md5(rand());
	$sujet = "Camagru: Activate your account!";
	$header = "From: CAMAGRU<maximilien.pressense>".$passage_ligne;
	$header.= "Reply-to: CAMAGRU<mpressen@student.42.fr>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
	$message = $passage_ligne."--".$boundary.$passage_ligne;
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	mail($email,$sujet,$message,$header);
	header('Location: ../../connexion.php?account_created=1');
}
else
	header('Location: ../../index.php');
?>