<?php

require_once("../../model/shared/user_id_from_login.php");
require_once("../../model/shared/formkey.class.php");

function auth($login, $pwd)
{
	global $DB;
	$prep = $DB->prepare('SELECT login, pwd, confirmation FROM users WHERE login = ? AND pwd = ?');
    $prep->execute(array($login, hash(whirlpool, $pwd)));
    $ret = $prep->fetch();
	return ($ret);
}

?>