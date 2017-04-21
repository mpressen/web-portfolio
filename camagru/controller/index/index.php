<?php
require_once("model/index/index.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);

if (!$user_id)
	$user_id = 0;

$formKey = new formKey();

$page = htmlspecialchars($_GET['page']);
if (!$page)
    $page = 1;
if ($page == 1 && isset($_GET['form_key']) && $formKey->validate2())
	$display = htmlspecialchars($_GET['display']);
if ($page > 1)
	$display = htmlspecialchars($_GET['display']);

$pictures_published_count = count_pictures_published();

if (!$display)
    $display = 10;
if (!($display == 5 || $display == 10 || $display == 20 || $display == 'all'))
    $display = 10;
else if ($display == 'all')
	$display = $pictures_published_count;

$selection = $page * $display - $display;

$all_pictures = get_pictures_gallery($selection, $display);

$max_index = ceil($pictures_published_count / $display);

require_once("view/index/index.php");

?>