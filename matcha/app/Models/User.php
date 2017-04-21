<?php

namespace App\Models;

use App\Models\Model;
use App\Models\UserHasTag;

class User extends Model
{
	private $id;
	private $email;
    private $username;
	private $firstname;
	private $lastname;
	private $password;
	private $token;
	private $age = null;
    private $gender = null;
    private $attraction = 'bisexual';
    private $bio;
    private $latitude;
	private $longitude;
	private $postal_code;
	private $locality;
	private $country;
    private $popularity;
	private $lastactivity;
	private $isconnected;
	private $tags = [];
	protected $table = 'user';

	public function __construct(array $user = null)
	{
		parent::__construct();
		if (isset($user) && !empty($user))
		{
			$this->arrayToUser($user);
		}
	}

	public function arrayToUser(array $user)
	{
		foreach($this as $key => $value) {
			if (array_key_exists($key, $user)){
				$this->$key = $user[$key];
			}
		}
	}

	public function isEmpty(){
		$attribute = get_object_vars(new Parent());
		foreach($this as $key => $value) {
			if (!array_key_exists($key, $attribute) && $value !== null)
				return (false);
		}
		return (true);
	}

	public function insert(){
        $statement =  "INSERT INTO ".$this->table." (email, username, firstname, lastname, password, token, age, gender, attraction, longitude, latitude, postal_code, country, locality) ";
        $statement .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $attributes = array(
            $this->email,
            $this->username,
            $this->firstname,
            $this->lastname,
            $this->password,
            $this->token,
            $this->age,
            $this->gender,
            $this->attraction,
            $this->longitude,
            $this->latitude,
            $this->postal_code,
            $this->country,
            $this->locality,
        );
        $req = $this->pdo->prepare($statement);
        $rep = $req->execute($attributes);
        return ($rep);
    }

	public function getUserById($id)
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "SELECT * FROM " . $this->table . " WHERE id = ? ";
            $attribute = array($id);
            $user = $this->execute($statement, $attribute, __CLASS__, true);
            return ($user);
        }
        else
            return false;
	}

	public function getUserByIdWithTags($id)
	{
		$options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "SELECT * FROM " . $this->table . " WHERE id = ? ";
            $attribute = array($id);
            $user = $this->execute($statement, $attribute, __CLASS__, true);
			if ($user){
				$tags = new UserHasTag();
				$tags = $tags->getAllTagsByUserId($user->getId());
				$tab = array();
				foreach ($tags as $key => $value) {
					$tab[] = $tags[$key]->cleanAttributParent($value);
				}
				$user->setTags($tab);
	            return ($user);
			}
        }
        return false;
	}

	public function getUserByEmail($email)
	{
		if (($email = filter_var($email, FILTER_VALIDATE_EMAIL)) !== false){
            $statement = "SELECT * FROM " . $this->table . " WHERE email = ? ";
            $attribute = array($email);
            $user = $this->execute($statement, $attribute, __CLASS__, true);
            return ($user);
        }
        else
            return false;
	}

	public function getUserByUsername($username)
	{
            $statement = "SELECT * FROM " . $this->table . " WHERE username = ? ";
            $attribute = array($username);
            $user = $this->execute($statement, $attribute, __CLASS__, true);
            return ($user);
	}

	public function emailExists($email)
	{
		$user = $this->getUserByEmail($email);
		if($user !== false)
            return (true);
        return (false);
	}

	public function usernameExists($username)
	{
		$user = $this->getUserByUsername($username);
		if($user !== false)
			return (true);
		return (false);
	}

	public function idExists($id)
	{
		$user = $this->getUserById($id);
		if($user !== false)
			return (true);
		return (false);
	}

	public function generateToken(){
		$token = str_replace(array('.', '/'), '',password_hash(microtime(true) * 100000, PASSWORD_DEFAULT));
		$this->setToken($token);
	}

	public function updateLocation($id, \app\Location\Location $location)
	{
		if (!empty($id) &&
			!empty($location->getPostalCode()) &&
			!empty($location->getlocality()) &&
			!empty($location->getCountry()) &&
			!empty($location->getLatitude()) &&
			!empty($location->getLongitude())){
			$statement =  "UPDATE ".$this->table." ";
			$statement .= "SET postal_code = ?, locality = ?, country = ?, latitude = ?, longitude = ? WHERE id = ?;";
			$attributes = array($location->getPostalCode(), $location->getlocality(), $location->getCountry(), $location->getLatitude(), $location->getLongitude() ,$id);
			$req = $this->pdo->prepare($statement);
			$req->execute($attributes);
		}

	}

    /**
     * Get the value of Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of Id
     *
     * @param mixed id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Email
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of Email
     *
     * @param mixed email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of Username
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of Username
     *
     * @param mixed username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of Firstname
     *
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of Firstname
     *
     * @param mixed firstname
     *
     * @return self
     */
    public function setFirstname($firstname)
    {

        $this->firstname = ucfirst($firstname);

        return $this;
    }

    /**
     * Get the value of Lastname
     *
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of Lastname
     *
     * @param mixed lastname
     *
     * @return self
     */
    public function setLastname($lastname)
    {
	    $this->lastname = ucfirst($lastname);
        return $this;
    }

    /**
     * Get the value of Password
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of Password
     *
     * @param mixed password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of Key
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of Key
     *
     * @param mixed key
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of Age
     *
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set the value of Age
     *
     * @param mixed age
     *
     * @return self
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get the value of Gender
     *
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set the value of Gender
     *
     * @param mixed gender
     *
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get the value of Attraction
     *
     * @return mixed
     */
    public function getAttraction()
    {
        return $this->attraction;
    }

    /**
     * Set the value of Attraction
     *
     * @param mixed attraction
     *
     * @return self
     */
    public function setAttraction($attraction)
    {
        $this->attraction = $attraction;

        return $this;
    }

    /**
     * Get the value of Bio
     *
     * @return mixed
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set the value of Bio
     *
     * @param mixed bio
     *
     * @return self
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get the value of Latitude
     *
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the value of Latitude
     *
     * @param mixed latitude
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get the value of Longitude
     *
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the value of Longitude
     *
     * @param mixed longitude
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get the value of Postal Code
     *
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * Set the value of Postal Code
     *
     * @param mixed postal_code
     *
     * @return self
     */
    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * Get the value of Locality
     *
     * @return mixed
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set the value of Locality
     *
     * @param mixed locality
     *
     * @return self
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get the value of Country
     *
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of Country
     *
     * @param mixed country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of Popularity
     *
     * @return mixed
     */
    public function getPopularity()
    {
        return $this->popularity;
    }

    /**
     * Set the value of Popularity
     *
     * @param mixed popularity
     *
     * @return self
     */
    public function setPopularity($popularity)
    {
        $this->popularity = $popularity;

        return $this;
    }

    /**
     * Get the value of lastActivity
     *
     * @return mixed
     */
    public function getLastActivity()
    {
        return $this->lastactivity;
    }

    /**
     * Set the value of lastActivity
     *
     * @param mixed lastActivity
     *
     * @return self
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastactivity = $lastActivity;

        return $this;
    }


    /**
     * Get the value of Isconnected
     *
     * @return mixed
     */
    public function getIsconnected()
    {
		if (!empty($this->lastactivity) && $this->isconnected){
			$datetime = new \DateTime($this->lastactivity);
			$now = new \DateTime("now");
			$interval = $datetime->modify('+15 minutes');
			if ($now > $interval){
				$this->isconnected = false;
			}
		};
        return $this->isconnected;
    }

    /**
     * Set the value of Isconnected
     *
     * @param mixed isconnected
     *
     * @return self
     */
    public function setIsconnected($isconnected)
    {
        $this->isconnected = $isconnected;

        return $this;
    }


    /**
     * Get the value of Tags
     *
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the value of Tags
     *
     * @param mixed tags
     *
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

}