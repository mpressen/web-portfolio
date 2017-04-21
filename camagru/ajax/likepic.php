<?php

require_once("../config/setup.php");
require_once("../model/index/likepic.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$picture_id = htmlspecialchars($_POST["picture_id"]);
if ($user_id && $picture_id)
{
	$already_liked_by_user = if_liked_by_user($picture_id, $user_id);
	
	if ($already_liked_by_user)
	{
		delete_user_like($picture_id, $user_id);
		echo "1";
	}
	else
		add_user_like($user_id, $picture_id);
}
else
    header('Location: ../connexion.php');

?>