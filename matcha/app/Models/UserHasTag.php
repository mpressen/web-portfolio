<?php
namespace App\Models;

use App\Models\Model;
use App\Models\Tag;

class UserHasTag extends Model
{
    private $user_id;
    private $tag_id;
    private $name;
    protected $table = "user_has_tag";

    public function __construct(array $usertag = null)
	{
		parent::__construct();
		if (isset($usertag) && !empty($usertag))
		{
			$this->arrayToUserHasTag($usertag);
		}
	}

    public function arrayToUserHasTag(array $usertag)
	{
		foreach($this as $key => $value) {
			if (array_key_exists($key, $usertag)){
				$this->$key = $usertag[$key];
			}
		}
	}

    public function addTagByUserId($user_id, $name)
    {
        $options = array('options' => array( 'min_range' => 1) );
        if (($user_id = filter_var($user_id, FILTER_VALIDATE_INT, $options)) !== false){
            $tags = new Tag();
            $tag_id = $tags->addTag($name);
            if ($this->existTag($tag_id, $user_id) === false){
                $statement = "INSERT INTO ".$this->table." (tag_id, user_id)";
                $statement .= "VALUES (?, ?)";
                $attributes = array($tag_id, $user_id);
                $req = $this->pdo->prepare($statement);
        		$rep = $req->execute($attributes);
            }
        }
    }

    public function deleteTagByName($name, $user_id)
    {
        if (!empty($name) && !empty($user_id)){
            $model = new Tag();
            $tag = $model->getTagByName($name);
            $statement = "DELETE FROM ".$this->table." ";
            $statement .= "WHERE tag_id = ? AND user_id = ?";
            $attributes = array($tag->getId(), $user_id);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);
        }
    }

    public function existTag($tag_id, $user_id)
    {
        $statement = "SELECT * FROM " . $this->table . " ";
        $statement .= "WHERE user_id = ? AND tag_id = ?";
        $attributes = array($user_id, $tag_id);
        $tag = $this->execute($statement, $attributes, __CLASS__, true);
        if ($tag)
            return true;
        return false;
    }

    public function getAllTagsByUserId($user_id)
    {
        $options = array('options' => array( 'min_range' => 1) );
		if (($user_id = filter_var($user_id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "SELECT * FROM " . $this->table . " WHERE user_id = ? ";
            $attribute = array($user_id);
            $usertags = $this->execute($statement, $attribute, __CLASS__, false);
            $tag = new Tag();
            foreach ($usertags as $key => $value) {
                $tag = $tag->getTagById($value->getTagId());
                $usertags[$key]->setName($tag->getName());
            }
            return ($usertags);
        }
        else
            return false;
    }

    public function cleanAttributParent(UserHasTag $object)
    {
        $attribute = get_object_vars(new Parent());
        $tab = array();
        foreach($object as $key => $value) {
            if (!array_key_exists($key, $attribute))
                $tab[$key] = $value;
        }
        return (object)$tab;
    }

    /**
     * Get the value of User Id
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of User Id
     *
     * @param mixed user_id
     *
     * @return self
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of Tag Id
     *
     * @return mixed
     */
    public function getTagId()
    {
        return $this->tag_id;
    }

    /**
     * Set the value of Tag Id
     *
     * @param mixed tag_id
     *
     * @return self
     */
    public function setTagId($tag_id)
    {
        $this->tag_id = $tag_id;

        return $this;
    }

    /**
     * Get the value of Table
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set the value of Table
     *
     * @param mixed table
     *
     * @return self
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }


    /**
     * Get the value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

}