<?php

require_once("../../model/shared/user_id_from_login.php");
require_once("../../model/shared/formkey.class.php");

function login_already_in_database($login)
{
	global $DB;
	$prep = $DB->prepare('SELECT login FROM users WHERE login = ?');
    $prep->execute(array($login));
    $ret = $prep->fetch()[0];
	return ($ret);
}

function mail_already_in_database($email)
{
	global $DB;
	$prep = $DB->prepare('SELECT mail FROM users WHERE mail = ?');
    $prep->execute(array($email));
    $ret = $prep->fetch()[0];
	return ($ret);
}

function create_new_user($login, $pwd, $email)
{
	global $DB;
	$prep = $DB->prepare('INSERT INTO users(login, pwd, mail, confirmkey) VALUES(:login, :pwd, :mail, :confirmkey)');
    $key = "";
    for ($i = 0; $i < 15; $i++)
        $key .= mt_rand(0,9);
    $prep->execute(array(
					   'login' => $login,
					   'pwd' => hash(whirlpool, $pwd),
					   'mail' => $email,
					   'confirmkey' => $key));
	return($key);
}

?>