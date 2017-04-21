<?php
namespace App\UserList;

use App\Database\Database;

class UserList
{
	protected $min_age = null;
	protected $max_age = null;
	protected $min_popularity = null;
	protected $max_popularity = null;
	protected $tags = [];
	protected $location_lat = null;
	protected $location_lon = null;
	protected $num_gender = 0;
	protected $num_attraction = 0;
	protected $unattraction = null;
	protected $gender_unattraction = [];
	protected $num_range = 1;
	protected $num_order = 5;
	protected $num_limit = 1;
	protected $num_groupuser = 0;
	protected $username;
	protected $offset = 0;

	private $user_id;
	private $pdo;
	private $found_rows = 0;
	private $_select = "";
	private $_from = "";
	private $_group = "";
	private $_having = "";

	private $args = [];
	private $args_params = [];
	private $groupuser = [1, 2];
	private $attraction = [1 => 'bisexual',
		2 => 'heterosexual',
		3 => 'homosexual'];
	private $gender = [1 => 'male',
		2 => 'female'];
	private $range = [1 => 10,
		2 => 30,
		3 => 50,
		4 => 100];
	private $order = [1 => "`age` ASC",
		2 => "`age` DESC",
		3 => "`popularity` DESC",
		4 => "`popularity` ASC",
		5 => "`distance` ASC",
		6 => "`distance` DESC",
		7 => "COUNT(`tag`.`id`) DESC",
		8 => "COUNT(`tag`.`id`) ASC"
		];
	private $limit = [1 => 12,
		2 => 24,
		3 => 48];

	public function __construct( $user_id )
	{
		if (!empty($user_id))
			$this->user_id = $user_id;
		else
			die();
		$db = new Database();
		$this->pdo = $db->getPdo();
	}

	public function getList()
	{
		$this->_select = <<<SQL
SELECT SQL_CALC_FOUND_ROWS T0.*,
IFNULL((SELECT `url` FROM `user_image` WHERE `user_id` = T0.`id` AND `isactive` = 1 LIMIT 1), NULL) as `image`,
IFNULL((SELECT COUNT(`src_user_id`) FROM `likes` WHERE `dest_user_id` = T0.`id`), 0) as `like`,
IFNULL((SELECT COUNT(DISTINCT `src_user_id`) FROM `visited` WHERE `dest_user_id` = T0.`id`), 0) as `visit`
SQL;
		$this->_from = <<<SQL
 FROM `user` as T0 
SQL;
		$this->setArgs();

		$sql = $this->_select . $this->_from . implode(" AND ", $this->args) . " GROUP BY T0.`id`";
		if (!empty($this->_having))
		{
			$sql.= $this->_having;
			$this->args_params[] = $this->getRange();
		}
		$sql.= " ORDER BY " . $this->getOrder();
		$sql.= " LIMIT {$this->offset}," . $this->getLimit();

		$stmt = $this->pdo->prepare($sql);

		$stmt->execute($this->args_params);

		$this->setFoundRows();
//var_dump($sql, $this->args_params);
		return $stmt->fetchAll(\PDO::FETCH_CLASS, "App\Models\User");
	}

	private function setArgs()
	{
/*
 * Location
 */
		if (!is_null($this->location_lat) && !is_null($this->location_lon))
		{
			$this->_select.= <<<SQL
, (6371 * 2 * ASIN(SQRT(POWER(SIN((? - abs(T0.`latitude`)) * pi()/180 /2), 2) + COS(? * pi()/180) * COS(abs(T0.`latitude`) * pi()/180) * POWER(SIN((? - T0.`longitude`) * pi()/180 / 2), 2)))) as `distance`
SQL;
			$this->args_params[] = $this->location_lat;
			$this->args_params[] = $this->location_lat;
			$this->args_params[] = $this->location_lon;

			$this->_having = " HAVING `distance` <= ?";
		}
/*
 * Do not include current user
 */
		$this->args[] = " WHERE T0.`id` != ?";
		$this->args_params[] = $this->user_id;
/*
 * Blacklist
 */ 
		$this->args[] = " T0.`id` NOT IN (SELECT `dest_user_id` FROM `blacklist` WHERE `src_user_id` = ?)";
		$this->args_params[] = $this->user_id;
/*
 * Age
 */
		if (!is_null($this->min_age) && !is_null($this->max_age))
		{
			$this->args[] = "T0.`age` BETWEEN ? AND ?";
			$this->args_params[] = $this->min_age;
			$this->args_params[] = $this->max_age;
		}
/*
 * Username
 */
		if (!empty($this->username))
		{
			$this->args[] = "T0.`username` LIKE ?";
			$this->args_params[] = '%'.$this->username.'%';
		}
/*
 * User in Visited | Liked
 */
		if ($this->num_groupuser)
		{        
			switch ($this->num_groupuser)
			{
				case 1:
					$this->_from.= " INNER JOIN `visited` as T1 ON T1.`dest_user_id` = T0.`id`";
					break;
				
				case 2:
					$this->_from.= " INNER JOIN `likes` as T1 ON T1.`dest_user_id` = T0.`id`";
					break;
			}
			$this->args[] = "T1.`src_user_id` = ?";
			$this->args_params[] = $this->user_id;
		}
/*
 * Popularity
 */
		if (!is_null($this->min_popularity) && !is_null($this->max_popularity))
		{
			$this->args[] = "T0.`popularity` BETWEEN ? AND ?";
			$this->args_params[] = $this->min_popularity;
			$this->args_params[] = $this->max_popularity;
		}
/*
 * Tags
 */
		if (!empty($this->tags))
		{
			$this->_select.= <<<SQL
, GROUP_CONCAT(`tag`.`name` ORDER BY `tag`.`name` SEPARATOR ',') as `tags`
SQL;
			$this->_from.= <<<SQL
LEFT JOIN (`user_has_tag` INNER JOIN `tag` ON `user_has_tag`.`tag_id` = `tag`.`id`) ON `user_has_tag`.`user_id` = T0.`id`
SQL;
			$this->args[] = "`tag`.`name` IN (?" . str_repeat(', ?', count($this->tags) - 1) . ")";
			$this->args_params = array_merge($this->args_params, $this->tags);
		}
/*
 * Interesting or Non-interesting profiles
 */
		if (!empty($this->gender_unattraction))
		{
			foreach ($this->gender_unattraction as $key => $value)
			{
				$this->args[] = " CASE T0.`gender` WHEN ? THEN T0.`attraction` != ? ELSE 1=1 END";
				$this->args_params[] = $key;
				$this->args_params[] = $value;
			}
		}
		else
		{
			if (!empty($this->num_gender))
			{
				$this->args[] = "T0.`gender` = ?";
				$this->args_params[] = $this->gender[$this->num_gender];
			}
			if (!is_null($this->unattraction))
			{
				$this->args[] = "T0.`attraction` != ?";
				$this->args_params[] = $this->unattraction;
			}
			if (!empty($this->num_attraction))
			{
				$this->args[] = "T0.`attraction` = ?";
				$this->args_params[] = $this->attraction[$this->num_attraction];
			}
		}
	}

	private function getOrder()
	{
		if ($this->num_order == 5 || $this->num_order  == 6)
		{
			if (is_null($this->location_lat) || is_null($this->location_lon))
				$this->num_order = 1;
		}
		else if ($this->num_order == 7 || $this->num_order == 8)
		{
			if (empty($this->tags))
				$this->num_order = 1;
		}
		return $this->order[$this->num_order];
	}

	public function getRange()
	{
		return $this->range[$this->num_range];
	}

	public function getLimit()
	{
		return $this->limit[$this->num_limit];
	}

	public function getNumOrder()
	{
		return $this->num_order;
	}

	public function getNumRange()
	{
		return $this->num_range;
	}

	public function getNumLimit()
	{
		return $this->num_limit;
	}

	public function getNumGender()
	{
		return $this->num_gender;
	}

	public function getNumAttraction()
	{
		return $this->num_attraction;
	}

	public function getFoundRows()
	{
		return $this->found_rows;
	}

	public function getGroupUser()
	{
		return $this->groupuser;
	}

	public function getUsername()
	{
		return $this->username;
	}

	private function setFoundRows()
	{
		$this->found_rows = $this->pdo->query("SELECT FOUND_ROWS() as `rows`;")->fetch()['rows'];
	}

	public function setUsername( $username )
	{
		$this->username = $username;
	}

	public function setGender( $gender )
	{
		if (($k = array_search($gender, $this->gender)))
			$this->num_gender = $k;
	}

	public function setNumGender( $num_gender )
	{
		if (array_key_exists($num_gender, $this->gender))
			$this->num_gender = $num_gender;
		return $this;
	}

	public function setNumAttraction( $num_attraction )
	{
		if (array_key_exists($num_attraction, $this->attraction))
			$this->num_attraction = $num_attraction;
		return $this;
	}

	public function setUnattraction( $unattraction )
	{
		if (in_array($unattraction, $this->attraction))
			$this->unattraction = $unattraction;
		return $this;
	}

	public function setGenderUnattraction( $gender , $unattraction )
	{
		if (in_array($gender, $this->gender)
			&& in_array($unattraction, $this->attraction))
			$this->gender_unattraction[$gender] = $unattraction;
		return $this;
	}

	public function setMinAge( $min_age )
	{
		$this->min_age = $min_age;
		return $this;
	}

	public function setMaxAge( $max_age )
	{
		$this->max_age = $max_age;
		return $this;
	}

	public function setMinPopularity( $min_popularity )
	{
		$this->min_popularity = $min_popularity;
		return $this;
	}

	public function setMaxPopularity( $max_popularity )
	{
		$this->max_popularity = $max_popularity;
		return $this;
	}

	public function setTags( array $tags )
	{
		$this->tags = $tags;
		return $this;
	}

	public function setLocation( $latitude , $longitude )
	{
		$this->location_lat = $latitude;
		$this->location_lon = $longitude;
		return $this;
	}

	public function setNumRange( $num_range )
	{
		if (array_key_exists($num_range, $this->range))
			$this->num_range = $num_range;
		return $this;
	}

	public function setNumOrder( $num_order )
	{
		if (array_key_exists($num_order, $this->order))
			$this->num_order = $num_order;
		return $this;
	}

	public function setNumLimit( $num_limit )
	{
		if (array_key_exists($num_limit, $this->limit))
			$this->num_limit = $num_limit;
		return $this;
	}

	public function setOffset( $offset )
	{
		if (is_numeric($offset) && $offset > 1)
		{
			$offset -= 1;
			$this->offset = $offset * $this->getLimit();
		}
		return $this;
	}

	public function setGroupUser( $num_groupuser )
	{
		if (in_array($num_groupuser, $this->groupuser))
			$this->num_groupuser = $num_groupuser;
		return $this;
	}
}