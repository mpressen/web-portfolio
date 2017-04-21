<?php

namespace App\Models;

use App\Models\Model;

class ChatMessage extends Model
{
    private $id;
    private $message;
    private $chat_id;
    private $user_id;
    private $create_time;
    protected $table = "chat_message";


    public function getConversationByChatId($chat_id)
    {
        $options = array('options' => array( 'min_range' => 1) );
        if (($chat_id = filter_var($chat_id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = 'SELECT chat_message.*, user.username, user.id FROM '.$this->table.' ';
            $statement .= 'INNER JOIN user ON '.$this->table.'.user_id = user.id ';
            $statement .= 'WHERE '.$this->table.'.chat_id = ? ';
            $statement .= ' ORDER BY '.$this->table.'.id';
            $attribut = array($chat_id);
            $chat_message = $this->execute($statement, $attribut, __CLASS__, false);
            return $chat_message;
        }
        return false;
    }

    public function AddMessageByChatId($chat_id, $user_id, $message)
    {
        $statement = "INSERT INTO ".$this->table." (chat_id, user_id, message) ";
        $statement .= "VALUES(?, ?, ?) ";
        $attributes = array($chat_id, $user_id, $message);
        $req = $this->pdo->prepare($statement);
		$rep = $req->execute($attributes);
        return ($rep);
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
     * Get the value of Chat Id
     *
     * @return mixed
     */
    public function getChatId()
    {
        return $this->chat_id;
    }

    /**
     * Set the value of Chat Id
     *
     * @param mixed chat_id
     *
     * @return self
     */
    public function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;

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