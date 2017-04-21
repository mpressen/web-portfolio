<?php
require_once("../../config/setup.php");
require_once("../../model/shared/user_id_from_login.php");
require_once("../../model/shared/formkey.class.php");

function clear_folder($folder_name)
{
	$folder = opendir($folder_name);
	while ($file = readdir($folder))
	{
		if ($file != "." && $file != "..")
			unlink($folder_name.$file);
	}
	closedir($folder);
}

function create_folder($folder)
{
    if (file_exists($folder) && is_dir($folder))
	{
		clear_folder($folder);
        return ($folder);
	}
    mkdir($folder);
    return($folder);
}

function resize($file)
{
	$x = 640;
	$y = 480;
	$size = getimagesize($file);
	if ($size)
	{
		if ($size['mime'] =='image/jpeg')
		{
			$img_big = imagecreatefromjpeg($file);
			$img_new = imagecreate($x, $y);
			$img_mini = imagecreatetruecolor($x, $y);
			imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
			imagejpeg($img_mini,$file );
		}
		else if ($size['mime']=='image/png' )
		{
			$img_big = imagecreatefrompng($file);
			$img_new = imagecreate($x, $y);
			$img_mini = imagecreatetruecolor($x, $y);
			imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
			imagepng($img_mini,$file );
		}
		else if ($size['mime']=='image/gif' )
		{
			$img_big = imagecreatefromgif($file);
			$img_new = imagecreate($x, $y);
			$img_mini = imagecreatetruecolor($x, $y);
			imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
			imagegif($img_mini,$file );
		}
	}
}

$user_id = user_id_from_login($_SESSION['loggued_on_user']);
$formKey = new formKey();

if ($user_id && isset($_POST['form_key']) && $formKey->validate()) 
{
	$name_file = htmlspecialchars($_FILES['fileToUpload']['name']);
	if ( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file) )
	{
		header('Location: ../../play.php?error=1');
		exit;
	}
	$target_dir = create_folder("../../uploads/");
	$target_file = $target_dir . basename($name_file);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	if (isset($_POST["submit"]))
	{
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false)
			$uploadOk = 1;
		else 
			$uploadOk = 0;
	}
	else
	{
		header('Location: ../../play.php?error=1');
		exit;
	}
	if (file_exists($target_file))
	{
		header('Location: ../../play.php?upload='.$target_file);
		exit;
	}
	if (htmlspecialchars($_FILES["fileToUpload"]["size"]) > 500000)
		$uploadOk = 0;
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	   && $imageFileType != "gif" )
		$uploadOk = 0;
	if ($uploadOk == 0)
		header('Location: ../../play.php?error=1');
	else
	{
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
		{
			resize($target_file);
			header('Location: ../../play.php?upload='.substr($target_file, 6));
		}
		else 
			header('Location: ../../play.php?error=1');
	}
}
else
	header('Location: ../../connexion.php');
?>