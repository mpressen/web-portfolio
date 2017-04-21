<?php

require_once("model/shared/user_id_from_login.php");
require_once("model/shared/if_liked_by_user.php");
require_once("model/shared/formkey.class.php");

function get_pictures_gallery($offset, $limit)
{
	global $DB;
    $offset = (int) $offset;
    $limit = (int) $limit;
	$prep = $DB->prepare('SELECT * FROM pictures WHERE publish = 1 ORDER BY picture_id DESC LIMIT ?, ?');
	$prep->execute(array($offset, $limit));
	$ret = $prep->fetchAll();
	return ($ret); 
}

function count_likes($picture_id)
{
	global $DB;
	$prep = $DB->prepare('SELECT COUNT(*) FROM likes WHERE picture_id = ?');
	$prep->execute(array($picture_id));
	$ret = $prep->fetch()[0];
	return ($ret);
}

function count_comments($picture_id)
{
	global $DB;
	$prep = $DB->prepare('SELECT COUNT(*) FROM comments WHERE picture_id = ?');
	$prep->execute(array($picture_id));
	$ret = $prep->fetch()[0];
	return ($ret);
}

function if_commented_by_user($picture_id, $user_id)
{
	global $DB;
	$prep = $DB->prepare('SELECT * FROM comments WHERE picture_id = ? AND user_id = ?');
	$prep->execute(array($picture_id, $user_id));
	$ret = $prep->fetch();
	return ($ret);
}

function count_pictures_published()
{
	global $DB;
	$ret = $DB->query('SELECT COUNT(*) FROM pictures WHERE publish = 1')->fetch()[0];
	return ($ret);
}

?>


