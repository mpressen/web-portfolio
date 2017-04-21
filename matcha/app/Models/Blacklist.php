<?php

namespace App\Models;

use App\Models\Model;

class Blacklist extends Model
{
	private $src_user_id;
	private $dest_user_id;
	protected $table = "blacklist";

	public function isBlock($src_id, $dest_id)
	{
		$data = $this->getBlacklist($src_id, $dest_id);
		if ($data)
			return true;
		return false;
	}

	public function insert()
	{
		$statement = "INSERT INTO `{$this->table}` (`src_user_id`, `dest_user_id`) VALUES (?, ?)";
		try
		{
			$this->pdo->prepare($statement)->execute([$this->src_user_id, $this->dest_user_id]);
			return true;
		}
		catch(\PDOException $e)
		{
			return false;
		}
	}

	public function delete()
	{
		$statement = "DELETE FROM `{$this->table}` WHERE `src_user_id` = ? AND `dest_user_id` = ?";
		try
		{
			$this->pdo->prepare($statement)->execute([$this->src_user_id, $this->dest_user_id]);
			return true;
		}
		catch(\PDOException $e)
		{
			return false;
		}
	}

	public function getAllBlackListByDestUserId($id)
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `{$this->table}` WHERE `dest_user_id` = ?";
			return $this->execute($statement, [$id], __CLASS__);
		}
	}

	public function getAllBlackListBySrcUserId($id)
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `{$this->table}` WHERE `src_user_id` = ?";
			return $this->execute($statement, [$id], __CLASS__);
		}
	}

	public function getBlacklist($src, $dest)
	{
		$statement = "SELECT * FROM `{$this->table}` WHERE `src_user_id` = ? AND `dest_user_id` = ?";
		return $this->execute($statement, [$src, $dest], __CLASS__, true);
	}

	public function clearBlacklistOfUser($id)
	{
		$statement = "DELETE FROM `{$this->table}` WHERE `src_user_id` = ?";
		try
		{
			$this->pdo->prepare($statement)->execute([$id]);
			return true;
		}
		catch(\PDOException $e)
		{
			return false;
		}
	}

	public function setSrcUserId($id)
	{
		$this->src_user_id = $id;

		return $this;
	}

	public function getSrcUserId()
	{
		return $this->src_user_id;
	}

	public function setDestUserId($id)
	{
		$this->dest_user_id = $id;

		return $this;
	}

	public function getDestUserId()
	{
		return $this->dest_user_id;
	}
}