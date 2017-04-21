<?php

require_once("model/index/commentpic/commentpic.php");

$formKey = new formKey();

if (isset($_GET['user_id']) && isset($_GET['picture_id']) && $_GET['user_id'] !== '' && $_GET['picture_id'] !== '')
{
	$user_id = htmlspecialchars($_GET['user_id']);
	$picture_id = htmlspecialchars($_GET['picture_id']);
	
	if ($user_id != user_id_from_login($_SESSION['loggued_on_user']))
		$user_id = 0;
	
	if (!($picture = picture_from_picture_id($picture_id)) || !$picture['publish'])
	{
		header('Location: index.php');
		exit;
	}
	
	$all_comments = get_comments($picture_id);
	$logins = get_logins_of_commenters($all_comments);
	
	require_once("view/index/commentpic.php");
}
else
	header('Location: index.php');

?>