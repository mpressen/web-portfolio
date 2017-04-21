<?php

namespace App\Models;

use App\Models\Model;

class Report extends Model
{
	private $src_user_id;
	private $dest_user_id;
	protected $table = "report";

	public function insert()
	{
		$statement = "INSERT INTO `{$this->table}` (`src_user_id`, `dest_user_id`) VALUES (?, ?)";
		return $this->pdo->prepare($statement)->execute([$this->src_user_id, $this->dest_user_id]);
	}

	public function getAllReportByDestUserId($id)
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `{$this->table}` WHERE `dest_user_id` = ?";
			return $this->execute($statement, [$id], __CLASS__);
		}
	}

	public function getAllReportBySrcUserId($id)
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `{$this->table}` WHERE `src_user_id` = ?";
			return $this->execute($statement, [$id], __CLASS__);
		}
	}

	public function getReport($src, $dest)
	{
		$statement = "SELECT * FROM `{$this->table}` WHERE `src_user_id` = ? AND `dest_user_id` = ?";
		return $this->execute($statement, [$src, $dest], __CLASS__);
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