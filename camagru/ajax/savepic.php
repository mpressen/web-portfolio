<?php
require_once("../config/setup.php");
require_once("../model/play/savepic.php");

function create_folder($folder)
{
	if (file_exists($folder) && is_dir($folder))
		return ($folder);
	mkdir($folder);
	return($folder);
}

function download_img($img, $folder, $file)
{
	$fd = fopen($folder."/".$file,'w');
	fwrite($fd, $img);
	fclose($fd);
}

$imageData = htmlspecialchars($_POST["base64data"]);
$flip = htmlspecialchars($_POST["flip"]);
$user_id = user_id_from_login($_SESSION['loggued_on_user']);

if ($imageData && $flip !== NULL && $user_id)
{
	add_new_picture($user_id, $flip);
	$name_picture = get_last_picture_id($user_id);
	$filteredData = str_replace(" ", "+", $imageData);
	$filteredData = substr($filteredData, strpos($filteredData, ",") + 1);
	$unencodedData = base64_decode($filteredData);
	$folder = create_folder('../pictures');
	download_img($unencodedData, $folder, $name_picture);
	echo $name_picture;
}
else
    header('Location: ../connexion.php');

?>