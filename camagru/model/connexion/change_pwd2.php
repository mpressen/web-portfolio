<?php

require_once("../../model/shared/user_id_from_login.php");
require_once("../../model/shared/formkey.class.php");

function login_and_mail_already_in_database($login, $email)
{
	global $DB;
	$prep = $DB->prepare('SELECT * FROM users WHERE login = ? and mail = ?');
    $prep->execute(array($login, $email));
    $ret = $prep->fetch();
	return ($ret);
}

?>