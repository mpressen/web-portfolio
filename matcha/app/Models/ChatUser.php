<?php

namespace App\Models;

use App\Models\Model;
use App\Models\User;
use App\Models\UserImage;
use \PDO;
class ChatUser extends Model
{
    private $chat_id;
    private $user_id;
    protected $table = "chat_user";


    public function insert($chat_id, $user_id)
    {
        $statement = "INSERT INTO " . $this->table . " VALUE(?, ?)";
        $req = $this->pdo->prepare($statement);
        return $req->execute([$chat_id, $user_id]);
    }

    public function getAllChatByUserId($user_id)
    {
        $options = array('options' => array( 'min_range' => 1) );
        if (($user_id = filter_var($user_id, FILTER_VALIDATE_INT, $options)) !== false){
            $statement = "SELECT * FROM ".$this->table." ";
            $statement .= "WHERE user_id = ?";
            $attribute = array($user_id);
            $data = $this->execute($statement, $attribute, __CLASS__, false);
            return($data);
        }
    }

    public function getAllContact($user_id)
    {
        $chatrooms = $this->getAllChatByUserId($user_id);
        $statement = "SELECT user.* FROM ".$this->table." ";
        $statement .= "LEFT JOIN user ON ".$this->table.".user_id = user.id ";
        $statement .= "WHERE ".$this->table.".chat_id = ? AND ".$this->table.".user_id != ?";
        $contacts = array();
        foreach ($chatrooms as $key => $value) {
            $attributes = array($value->getChatId(), $user_id);
            $contacts[$key]['user'] = $this->execute($statement, $attributes, 'App\Models\User', true);
            $image = new UserImage();
            $contacts[$key]['image'] = $image->getUserImageActive($contacts[$key]['user']->getId());
            $contacts[$key]['chat_id'] = $value->getChatId();
        }
        return $contacts;
    }

    public function chatIdAndUserIdExist($chat_id, $user_id)
    {
        $statement = "SELECT chat_id FROM ".$this->table." ";
        $statement .= "WHERE ".$this->table.".chat_id = ? AND ".$this->table.".user_id = ?";
        $attributes = array($chat_id, $user_id);
        $chat_user = $this->execute($statement, $attributes, __CLASS__, true);
        if ($chat_user){
            return true;
        }
        return false;
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

}