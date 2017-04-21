<?php

namespace App\Models;

use App\Models\Model;
use \PDO;

class Visited extends Model
{
	private $id;
	private $src_user_id;
	private $dest_user_id;
	private $visited_time;
	protected $table = "visited";

	public function insert()
	{
		$statement = "INSERT INTO `{$this->table}` (`src_user_id`, `dest_user_id`) VALUES (?, ?)";
		if ($this->pdo->prepare($statement)->execute([$this->src_user_id, $this->dest_user_id]))
		{
			$this->id = $this->pdo->lastInsertId();
			return true;
		}
		return false;
	}

	public function getVisitedById( $id )
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `{$this->table}` WHERE `id` = ?";
			return $this->execute($statement, [$id], __CLASS__, true);
		}
		return false;
	}

	public function getAllVisitedByDestUserId( $id )
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `visited` INNER JOIN `user` ON `user`.`id` = `visited`.`src_user_id` WHERE `dest_user_id` = ?";
			return $this->execute($statement, [$id], __CLASS__, false);
		}
		return [];
	}

	public function getAllVisitedBySrcUserId( $id )
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `user` INNER JOIN `visited` ON `visited`.`dest_user_id` = `user`.`id` WHERE `visited`.`src_user_id` = ? ORDER BY `visited`.`id` DESC";
			$attributes = array($id);
			$req = $this->pdo->prepare($statement);
			$rep = $req->execute($attributes);
			return $req->fetchAll(PDO::FETCH_ASSOC);
		}
		return [];
	}

	public function getVisitedBySrcUserIdDestUserId( $srcUserId , $destUserId )
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `visited` WHERE `visited`.`src_user_id` = ? AND `visited`.`dest_user_id` = ? ORDER BY `visited`.`id` DESC";
			$attributes = array($srcUserId, $destUserId);
			$req = $this->pdo->prepare($statement);
			$rep = $req->execute($attributes);
			return $req->fetchAll(PDO::FETCH_ASSOC);
		}
		return [];
	}

	public function clearHistoryOfUser( $id )
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "DELETE FROM `visited` WHERE src_user_id = ?";
			$attributes = array($id);
			$req = $this->pdo->prepare($statement);
			$rep = $req->execute($attributes);
		}
	}

	public function clearHistoryEntry( $id )
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "DELETE FROM `visited` WHERE id = ?";
			$attributes = array($id);
			$req = $this->pdo->prepare($statement);
			$rep = $req->execute($attributes);
		}
	}

	public function getUserIdfromVisitedEntry($id)
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
			$statement = "SELECT * FROM `visited` WHERE id = ?";
			$attributes = array($id);
			$req = $this->pdo->prepare($statement);
			$rep = $req->execute($attributes);
			$ret = $req->fetch(PDO::FETCH_ASSOC);
            return $ret['src_user_id'];
		}
	}

	public function getId()
	{
		return $this->id;
	}

	public function setSrcUserId( $id )
	{
		$this->src_user_id = $id;

		return $this;
	}

	public function getSrcUserId()
	{
		return $this->src_user_id;
	}

	public function setDestUserId( $id )
	{
		$this->dest_user_id = $id;

		return $this;
	}

	public function getDestUserId()
	{
		return $this->dest_user_id;
	}

	public function setVisitedTime( $visited_time )
	{
		$this->visited_time = $visited_time;

		return $this;
	}

	public function getVisitedTime()
	{
		return $this->visited_time;
	}
}