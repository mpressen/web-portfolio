<?php
require_once("model/shared/user_id_from_login.php");
require_once("model/shared/formkey.class.php");

function get_my_pictures($user_id, $offset, $limit)
{
	global $DB;
    $offset = (int) $offset;
    $limit = (int) $limit;
	$prep = $DB->prepare('SELECT * FROM pictures WHERE user_id = ? ORDER BY picture_id DESC LIMIT ?, ?');
	$prep->execute(array($user_id, $offset, $limit));
	$ret = $prep->fetchAll();
	return ($ret); 
}

function count_my_pictures($user_id)
{
	global $DB;
	$prep = $DB->prepare('SELECT COUNT(*) FROM pictures WHERE user_id = ?');
	$prep->execute(array($user_id));
	$ret = $prep->fetch()[0];
	return ($ret);
}

?>