<?php

namespace App\Database;

use \PDO;

class Database{

	private static $pdo = false;

	public function __construct()
	{
		if (self::$pdo === false)
		{
			try{
				require $_SERVER["DOCUMENT_ROOT"] . DS . '..' . DS . 'app' . DS . 'Config' . DS .'database.php';
				$pdo = new PDO($dsn."dbname=".$name.";charset=".$charset.";", $user, $password);
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e){
				if ($e->getCode() === 1049)
					die ("Connection failed: Unknown database ".$name);
				else
					die ("Connection failed: " . $e->getMessage());
			}
			self::$pdo = $pdo;
		}

	}

	public function getPdo()
	{
		return self::$pdo;
	}
}