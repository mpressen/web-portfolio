<?php

require_once("../../config/setup.php");
require_once("../../model/index/commentpic/commentpic2.php");

$formKey = new formKey();

if (!($user_loggued = user_id_from_login($_SESSION['loggued_on_user'])))
{
    header('Location: ../../index.php');
	exit;
}

if (isset($_GET['user_id']) && isset($_GET['picture_id']) && $_GET['user_id'] !== '' && $_GET['picture_id'] !== '')
{
	$user_id = htmlspecialchars($_GET['user_id']);
	$picture_id = htmlspecialchars($_GET['picture_id']);
	
	if ($user_id != $user_loggued || !($picture = picture_from_picture_id($picture_id)) || !$picture['publish'])
		header('Location: ../../index.php');
	else if (isset($_POST['comment_message']) && isset($_POST['comment_submit']) && $_POST['comment_message'] !== "" && strlen(($new_comment = htmlspecialchars($_POST['comment_message']))) <= 500 && isset($_POST['form_key']) && $formKey->validate())
	{
		create_comment($user_id, $picture_id, $new_comment);
		
		//envoyer un mail au proprio de l'image si ce n'est pas lui qui a commente sa propre image
		$mailto = get_user_id_from_picture_id($picture_id);
		if ($mailto && $mailto != $user_id && $mail_infos = get_mail_infos_from_user_id($mailto))
		{
			$login = $mail_infos[0];
			$email = $mail_infos[1];
			if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $email))
				$passage_ligne = "\r\n";
			else
				$passage_ligne = "\n";
			$path = $_SERVER['HTTP_HOST'].dirname(dirname(dirname(htmlspecialchars($_SERVER['PHP_SELF']))));
			$message_txt = "Hello ".$login.", one of your picture has just been commented, click on this link to check it out :";
			$message_html = "<html><head></head><body><p>Hello ".$login.", one of your picture has just been commented, click on this link to check it out :<br><a href='http://".$path."/commentpic.php?user_id=".$mailto."&picture_id=".$picture_id."'>See the comment</a></body></html>";
			$boundary = "-----=".md5(rand());
			$sujet = "Camagru: Someone has commented your picture!";
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
		}
		header('Location: ../../commentpic.php?user_id='.$user_id.'&picture_id='.$picture_id.'');
	}
	else
		header('Location: ../../commentpic.php?user_id='.$user_id.'&error=1&picture_id='.$picture_id.'');
}
else
	header('Location: ../../index.php');

?>