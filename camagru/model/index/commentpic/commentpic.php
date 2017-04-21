<?php

require_once("model/shared/user_id_from_login.php");
require_once("model/shared/picture_from_picture_id.php");
require_once("model/shared/formkey.class.php");

function get_comments($picture_id)
{
	global $DB;
	$prep = $DB->prepare('SELECT * FROM comments WHERE picture_id = ? ORDER BY timestamp');
	$prep->execute(array($picture_id));
	$ret = $prep->fetchAll();
	return ($ret); 
}

function get_logins_of_commenters($all_comments)
{
	global $DB;
	$logins = array();
	$prep = $DB->prepare('SELECT login FROM users WHERE user_id = ?');
	foreach ($all_comments as $comment)
	{
		$prep->execute(array($comment['user_id']));
		$logins[] = $prep->fetch()[0];
	}
	return ($logins);
}

?>