<?php
define("DS", DIRECTORY_SEPARATOR);
define("DIR_CURR", __DIR__ . DS);
define("DB_PATH", DIR_CURR ."database.php");
define("UPLOAD_PATH", __DIR__.DS.'../../public/assets/uploads');

function DeleteUploads($dir_path) 
{
	if (file_exists($dir_path) === true && is_dir($dir_path) === true) 
	{
		$dir = scandir($dir_path);
		$dir = array_slice($dir, 2);
		if (!empty($dir))
		{
			foreach ($dir as $elem) 
			{
				$subdir_path = $dir_path.DS.$elem;
				if (is_dir($subdir_path) === true)
				{
					$subdir = scandir($subdir_path);
					$subdir = array_slice($subdir, 2);
					if (!empty($subdir))
					{
						foreach ($subdir as $key => $value) 
						{
							$file_path = $subdir_path.DS.$value;
							if (is_file($file_path) === true) 
							{
								if  (unlink($file_path) === true)
									echo "\033[32msuppression de l'image ".$value." reussi.\n\033[0m";
								else
									echo "\033[31mSuppression de l'image ".$value." echoue.\033[0m\n";
							}
						}
					}
					if (rmdir($subdir_path) === true)
						echo "\033[32msuppression du dossier ".$elem." reussi.\n\033[0m";
					else
						echo "\033[31mSuppression du dossier ".$elem." echoue.\033[0m\n";
				}
				else
					echo "\033[32m".$elem." n'est pas un dossier.\033[0m\n";
			}
		}
		else
			echo "\033[32mLe dossier uploads est vide.\033[0m\n";
	}
	else
		echo "\033[31mLe dossier Uploads n'existe pas.\033[0m\n";
}

function executefilesql($path, $dbname = false)
{
	require(DB_PATH);
	try{
		$param = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
		if ($dbname === true)
			$pdo = new PDO($dsn."dbname=".$name.";", $user, $password, $param);
		else
			$pdo = new PDO($dsn, $user, $password, $param);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if (file_exists($path)){
			$file = file_get_contents($path);
			$tabquery = explode("\n", $file);
			$query = "";
			foreach($tabquery as $line)
			{
				if (!empty($line) && $line[0] !== '-')
					$query .= $line;
			}
			try{
				$bool = $pdo->prepare($query)->execute();
			}
			catch(PDOException $e){
				 echo $e->getMessage();
			}
			if ($bool)
				echo "\033[32mOperation reussi.\n\033[0m";
		}
		else
			echo "\033[31mThe file ".$path." not exists.\n\033[0m";
	}
	catch (PDOException $e){
		echo "\033[31mERREUR PDO dans ". $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage()."\033[0m\n";
	}
}

function print_menu()
{
	echo "1 - Create schema database.\n";
	echo "2 - Dumper database.\n";
	echo "3 - supprimer le répertoire uploads.\n";
	echo "0 - Exit.\n";
	echo "Hit the number of your choice: ";
}

if (!file_exists(DB_PATH))
{
	echo "\033[31mLe fichier database.php n'existe pas.\033[0m\n";
}
else
{
	require_once(DB_PATH);
	print_menu();
	while ($choice = fgets(STDIN))
	{
		$choice = trim($choice);
		$choice = (ctype_digit($choice)) ? intval($choice) : NULL ;
		if ($choice === 0)
			break;
		else if ($choice > 3 || $choice < 1)
		{
			echo "\033[31mInvalid command.\033[0m\n";
			print_menu();
		}
		else
		{
			if ($choice === 1)
				executefilesql(DIR_CURR. $schema);
			else if ($choice === 2)
				executefilesql(DIR_CURR. $dump, true);
			else if ($choice === 3)
				DeleteUploads(UPLOAD_PATH);
			print_menu();
		}
	}
}
?>