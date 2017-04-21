<?php
header('Content-Type: image/png');

require_once("../config/setup.php");
require_once("../model/index/likepic.php");

$user_id = user_id_from_login($_SESSION['loggued_on_user']);

if (!$user_id)
{
    header('Location: ../index.php');
	exit;
}

$imageData = htmlspecialchars($_POST["base64data"]);
$frame = htmlspecialchars($_POST["frame"]);

if ($imageData && $frame)
{
	$filteredData = str_replace(" ", "+", $imageData);
	$filteredData = substr($filteredData, strpos($filteredData, ",") + 1);
	$unencodedData = base64_decode($filteredData);
	$destination = imagecreatefromstring($unencodedData);


	$largeur_destination = imagesx($destination);
	$hauteur_destination = imagesy($destination);


	if ($frame == 3)
	{
		$source = imagecreatefrompng("../camagru_pics/frame3.png");
		$largeur_source = imagesx($source);
		$hauteur_source = imagesy($source);
		$destination_x = round(($largeur_destination - $largeur_source) * 0.5);
		$destination_y = round(($hauteur_destination - $hauteur_source) * 0.3);
	}
	else if ($frame == 2)
	{
		$source = imagecreatefrompng("../camagru_pics/frame2.png");
		$largeur_source = imagesx($source);
		$hauteur_source = imagesy($source);
		$destination_x = round(($largeur_destination - $largeur_source) * 0.5);
		$destination_y = round(($hauteur_destination - $hauteur_source) * 0.1);
	}
	else if ($frame == 1)
	{
		$source = imagecreatefrompng("../camagru_pics/frame1.png");
		$largeur_source = imagesx($source);
		$hauteur_source = imagesy($source);
		$destination_x = round(($largeur_destination - $largeur_source) * 0.5);
		$destination_y = round(($hauteur_destination - $hauteur_source) * 0.5);
	}

	imagealphablending($source, true);
	imagesavealpha($source, true);
	imagecopy($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source);

	ob_start();
	imagepng($destination, NULL);
	$contents = ob_get_contents();
	ob_end_clean();

	imagedestroy($destination);
	echo base64_encode($contents);
}
else
    header('Location: ../index.php');
?>