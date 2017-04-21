<?php
require_once("../../config/setup.php");
require_once("../../model/connexion/change_pwd2.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$formKey = new formKey();

if (!$user_id && isset($_POST['change_login']) && isset($_POST['change_email']) && $_POST['change_login'] !== "" && $_POST['change_email'] !== "" && $_POST['change_submit'] === 'OK' && isset($_POST['form_key']) && $formKey->validate())
{
    $login = htmlspecialchars($_POST["change_login"]);
    $email = htmlspecialchars($_POST["change_email"]);
    if (!preg_match("/^[a-zA-Z ]*$/",$login))
		header('Location: ../../change_pwd.php?value=1');
	else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		header('Location: ../../change_pwd.php?value=2');
	else if ($user = login_and_mail_already_in_database($login, $email))
	{
		$key = $user['confirmkey'];
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $email))
			$passage_ligne = "\r\n";
		else
			$passage_ligne = "\n";
		$path =$_SERVER['HTTP_HOST'].dirname(htmlspecialchars($_SERVER['PHP_SELF']));
		$message_txt = "Hello ".$login.", click on this link to reset your password!";
		$message_html = "<html><head></head><body><p>Hello ".$_POST['change_login'].", click on this link to reset your password!<br><a href='http://".$path."/verif.php?login=".$_POST['change_login']."&change_pwd=1&confirmkey=".$key."'>Reset your password</a></body></html>";
		$boundary = "-----=".md5(rand());
		$sujet = "Camagru: Reset your password!";
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
		header('Location: ../../change_pwd.php?value=3');
	}
	else
		header('Location: ../../change_pwd.php?value=4');			
}
else
	header('Location: ../../index.php');

?>