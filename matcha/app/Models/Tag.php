<?php

namespace App\Models;

use App\Models\Model as Model;

class Tag extends Model
{
    private $id;
    private $name;
    protected $table = 'tag';


    public function __construct(array $tag = null)
	{
		parent::__construct();
		if (isset($tag) && !empty($tag)) {
			$this->arrayToTag($tag);
		}
	}

    public function arrayToTag(array $tag)
	{
		foreach($this as $key => $value) {
			if (array_key_exists($key, $tag)){
				$this->$key = $usertag[$key];
			}
		}
	}

    public function getTagByName($name)
    {
        $statement = "SELECT * FROM " . $this->table . " ";
        $statement .= "WHERE name = ?";
        $attribute = array($name);
        $tag = $this->execute($statement, $attribute, __CLASS__, true);
        if ($tag)
            return ($tag);
        else
            return false;
    }

    public function addTag($name)
    {
        if (($tag = $this->getTagByName($name)) === false){
            $statement = "INSERT INTO ".$this->table." (name)";
            $statement .= "VALUES (?)";
            $attribute = array($name);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attribute);
            $this->setId($this->pdo->lastInsertId());
            $tag = $this;
            //$tag = $this->getTagByName($name);
        }
        return ($tag->getId());
    }


    public function getAllTags()
    {
        $statement = "SELECT * FROM " . $this->table;
        $tags = $this->execute($statement, $attribute, __CLASS__, false);
        $tab = array();
        foreach ($tags as $key => $value) {
           $tab[] = $tags[$key]->cleanAttributParent($value);
        }
        return ($tab);
    }

    public function getTagById($id)
    {
        $options = array('options' => array( 'min_range' => 1) );
		if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "SELECT name FROM " . $this->table . " WHERE id = ? ";
            $attribute = array($id);
            $tag = $this->execute($statement, $attribute, __CLASS__, true);
            return ($tag);
        }
        else
            return false;
    }

    public function cleanAttributParent(Tag $object)
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
