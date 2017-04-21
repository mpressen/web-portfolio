<?php

require_once("model/play/play.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$formKey = new formKey();

if (!$user_id)
{
	header('Location: index.php');
	exit;
}

if (isset($_GET['upload']))
{
	if (file_exists($_GET['upload']))
		$image = htmlspecialchars($_GET['upload']);
	else
		$_GET['error'] = 1;
}


$page = htmlspecialchars($_GET['page']);

if (!$page)
    $page = 1;

$pics_to_display = 5;

$selection = $page * $pics_to_display - $pics_to_display;

$all_my_pictures = get_my_pictures($user_id, $selection, $pics_to_display);

$my_pictures_count = count_my_pictures($user_id);

$max_index = ceil($my_pictures_count / $pics_to_display);

require_once("view/play/play.php");

?>