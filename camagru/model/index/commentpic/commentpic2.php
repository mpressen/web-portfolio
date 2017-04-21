<?php

require_once("../../model/shared/user_id_from_login.php");
require_once("../../model/shared/picture_from_picture_id.php");
require_once("../../model/shared/formkey.class.php");

function create_comment($user_id, $picture_id, $new_comment)
{
	global $DB;
	date_default_timezone_set('Europe/Paris');
	$prep = $DB->prepare('INSERT INTO comments(user_id, picture_id, comments) VALUES(?, ?, ?)');
	$prep->execute(array(intval($user_id), intval($picture_id), $new_comment));
}

function get_user_id_from_picture_id($picture_id)
{
	global $DB;
	$prep = $DB->prepare('SELECT user_id FROM pictures WHERE picture_id = ?');
	$prep->execute(array($picture_id));
	$ret = $prep->fetch()[0];
	return ($ret); 
}

function get_mail_infos_from_user_id($user_id)
{
	global $DB;
	$prep = $DB->prepare('SELECT login, mail FROM users WHERE user_id = ?');
	$prep->execute(array($user_id));
	$ret = $prep->fetch();
	return ($ret); 
}

?>