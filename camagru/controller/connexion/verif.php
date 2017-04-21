<?php

require_once("../../config/setup.php");
require_once("../../model/connexion/verif.php");
require_once("../../model/shared/formkey.class.php");

$formKey = new formKey();

// confirmer l'inscription ou afficher form pour create new passsword
if (isset($_GET["login"]) && isset($_GET["confirmkey"]) && !isset($_POST['change_submit']))
{
	$login = htmlspecialchars($_GET["login"]); 
	$confirmkey = htmlspecialchars($_GET['confirmkey']);
	if (user_from_login_and_confirmkey($login, $confirmkey))
	{
		if (!isset($_GET["change_pwd"]))
		{
			confirm_user($login);
			header('Location: ../../connexion.php?confirmed=1');
		}
		else
			header('Location: ../../recreate_pwd.php?login='.$login."&confirmkey=".$confirmkey);
	}
	else
		header('Location: ../../connexion.php?verif_error=1');
}
else if (isset($_GET["login"]) && isset($_POST["create_passwd"]) && isset($_POST["change_submit"]) && $_POST["create_passwd"] !== '' && $_POST["change_submit"] === 'OK' && isset($_POST['form_key']) && $formKey->validate())
{
    $login = htmlspecialchars($_GET["login"]);
    $new_pwd = htmlspecialchars($_POST['create_passwd']);
    if (strlen($new_pwd) < 5 || strlen($new_pwd) > 10 || !preg_match("#[0-9]+#", $new_pwd) || !preg_match("#[A-Z]+#", $new_pwd))
        $pwd_invalid = 1;
    if (!isset($pwd_invalid))
    {
		update_pwd($login, $new_pwd);
        header('Location: ../../connexion.php?pwd_changed=1');
    }
    else
        header('Location: ../../connexion.php?pwd_not_changed=1');
}
else
{
	header('Location: ../../connexion.php');
}

?>