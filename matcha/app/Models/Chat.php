<?php

namespace App\Models;

use App\Models\Model;
use App\Models\ChatUser;

class Chat extends Model
{
    private $id;
    private $create_time;
    protected $table = "chat";

    public function insert()
    {
        $statement = "INSERT INTO " . $this->table . " VALUE()";
        $req = $this->pdo->prepare($statement);
        $req->execute();
        return ($this->pdo->lastInsertId());
    }

    public function delete($id)
    {
        $statement = "DELETE FROM " . $this->table . " WHERE id=?";
        $req = $this->pdo->prepare($statement);
        return ($req->execute([$id]));
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
     * Get the value of Create Time
     *
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * Set the value of Create Time
     *
     * @param mixed create_time
     *
     * @return self
     */
    public function setCreateTime($create_time)
    {
        $this->create_time = $create_time;

        return $this;
    }

}
