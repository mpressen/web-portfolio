<?php

namespace App\Models;

use App\Models\Model as Model;
use \PDO;

class Notification extends Model
{
    private $id;
    private $message;
    private $link;
    private $type;
    private $isread;
    private $create_time;
    private $user_id;
    protected $table = "notification";

    public function getNotificationsByUserId($user_id)
    {
        $statement = "SELECT * FROM ". $this->table . " ";
        $statement .= "WHERE user_id = ? AND isread = 0 ORDER BY id desc";
        $attribut = array($user_id);
        $notifications = $this->execute($statement, $attribut, __CLASS__, false);
        return ($notifications);
    }

    public function getAllNotificationsByUserId($user_id)
    {
        $statement = "SELECT * FROM ". $this->table . " ";
        $statement .= "WHERE user_id = ? ORDER BY id desc";
        $attributes = array($user_id);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);
            return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addNotification($message, $type, $link, $user_id)
    {
        $statement = "INSERT INTO ". $this->table . " (message, type, link, user_id) ";
        $statement .= "VALUES (?, ?, ?, ?)";
        $attributes = array($message, $type, $link, $user_id);
        $req = $this->pdo->prepare($statement);
        $req->execute($attributes);
        return ($req);
    }

    public function updateStatus($array)
    {
        $statement = "UPDATE ". $this->table . " (message, type, link, user_id) ";
        $statement .= "VALUES (?, ?, ?, ?)";
        $attributes = array($message, $type, $link, $user_id);
        $req = $this->pdo->prepare($statement);
        $req->execute($attributes);
        return ($req);
    }

    public function getNotificationById($id)
    {
        $statement = "SELECT * FROM ". $this->table . " ";
        $statement .= "WHERE id = ? AND isread = 0";
        $attribut = array($id);
        $notification = $this->execute($statement, $attribut, __CLASS__, true);
        return ($notification);
    }

    public function clearNotificationsOfUser($id)
    {
        $options = array('options' => array( 'min_range' => 1) );
        if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "DELETE FROM `notification` WHERE user_id = ?";
            $attributes = array($id);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);
        }
    }

    public function clearNotificationsEntry($id)
    {
        $options = array('options' => array( 'min_range' => 1) );
        if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "DELETE FROM `notification` WHERE id = ?";
            $attributes = array($id);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);
        }
    }

    public function getUserIdfromNotificationEntry($id)
    {
        $options = array('options' => array( 'min_range' => 1) );
        if (($id = filter_var($id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "SELECT * FROM `notification` WHERE id = ?";
            $attributes = array($id);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);
            $ret = $req->fetch(PDO::FETCH_ASSOC);
            return $ret['user_id'];
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
     * Get the value of Message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of Message
     *
     * @param mixed message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of Type
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Type
     *
     * @param mixed type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of Isread
     *
     * @return mixed
     */
    public function getIsread()
    {
        return $this->isread;
    }

    /**
     * Set the value of Isread
     *
     * @param mixed isread
     *
     * @return self
     */
    public function setIsread($isread)
    {
        $this->isread = $isread;

        return $this;
    }

    /**
     * Get the value of Create Time
     *
     * @return mixed
     */
    public function getCreateTime()
    {
        $now = new \DateTime("now");
        $create_time = new \DateTime($this->create_time);
        $diff = $create_time->diff($now, true);
        if ($diff->y > 0)
            $this->create_time = $diff->format('%y Years ago');
        else if ($diff->m > 0)
            $this->create_time = $diff->format('%m Months ago');
        else if ($diff->d > 0)
            $this->create_time = $diff->format('%d Days ago');
        else if ($diff->h > 0)
            $this->create_time = $diff->format('%h Hours ago');
        else if ($diff->i > 0)
            $this->create_time = $diff->format('%i minutes ago');
        else
            $this->create_time = $diff->format('a moment ago');
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
     * Get the value of Link
     *
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the value of Link
     *
     * @param mixed link
     *
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

}
