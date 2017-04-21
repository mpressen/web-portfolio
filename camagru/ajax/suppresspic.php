<?php

require_once("../config/setup.php");
require_once("../model/play/suppresspic.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$picture_id = htmlspecialchars($_POST["picture_id"]);
$last_picture_id = htmlspecialchars($_POST["last_pic_id"]);

if ($user_id && $picture_id && $last_picture_id)
{
	delete_picture($picture_id, $user_id);
	$next_pics = get_my_next_pictures($user_id, $last_picture_id);
	if ($next_pics)
	{
		$response = "";
		foreach ($next_pics[0] as $elem)
			$response .= $elem.",";
		if ($next_pics[1])
			$response .= "1";
		echo $response;
	}
}
else
    header('Location: ../connexion.php');

?>
