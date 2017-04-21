<?php
namespace App\Models;

use App\Models\Model;
use \PDO;

class UserImage extends Model
{
    private $id;
    private $url;
    private $name;
    private $user_id;
    protected $table = "user_image";
    const UPLOAD_PATH = "assets/uploads/";

    public function addImage($file, $user_id, $pic_num)
    {
        //check if there is already a picture on the spot
        if (!($ret = self::imageExists($user_id, $pic_num)))
        {
            //if first picture, automatically set as profile pic
            if (!self::getImageProfilePosition($user_id))
                $isactive = 1;
            else
                $isactive = 0;

            $statement =  "INSERT INTO user_image (url, position, isactive, user_id)";
            $statement .= "VALUES (?, ?, ?, ?);";
            $attributes = array(substr($file, 1), $pic_num, $isactive, $user_id);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);
            return $isactive;
        }
        else
        {
            //delete file on folder
            unlink("." . $ret['url']);

            $statement =  "UPDATE user_image SET url = ? WHERE user_id = ? AND position = ?";
            $attributes = array(substr($file, 1), $user_id, $pic_num);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);
        }
    }

    public function deleteImage($position, $user_id)
    {
        //check if there's an image on the spot
        $ret = self::imageExists($user_id, $position);
        if ($ret['url'])
        {
            //delete file on folder
            unlink("." . $ret['url']);

            $statement =  "DELETE FROM user_image WHERE user_id = ? AND position = ?";
            $attributes = array($user_id, $position);
            $req = $this->pdo->prepare($statement);
            $rep = $req->execute($attributes);

            //if a profile pic is deleted, make another picture as profile pic, if possible
            if ($ret['isactive'] == 1)
            {
                $i = 1;
                while (self::setImageProfile($i, $user_id) == null)
                {
                    if ($i == 6)
                        break;
                    $i++;
                }

                //to display profile pic borders directly if there's an other picture
                if ($i < 6)
                    return json_encode($i);

            }
            return 'OK';
        }
    }

    public function setImageProfile($position, $user_id)
    {
        //check if there's an image on the spot
        if ($ret = self::imageExists($user_id, $position))
        {
            self::desactivateOldProfilePic($user_id);
            self::activateNewProfilePic($user_id, $position);
            //to display profile pic borders directly
            return $ret['url'];
        }
    }

    public function getImageProfilePosition($user_id)
    {
        $statement =  "SELECT * FROM user_image WHERE user_id = ? AND isactive = 1";
        $attributes = array($user_id);
        $req = $this->pdo->prepare($statement);
        $rep = $req->execute($attributes);
        if ($ret = $req->fetch(PDO::FETCH_ASSOC))
            return $ret['position'];
        return 0;
    }

    public function getImagesUrl($user_id)
    {
        $gallery = [];
        for ($i = 1; $i < 6; $i++)
        {
            if (($img = self::getImageUrl($user_id, $i)))
                $gallery[$i] = $img;
        }
        return $gallery;
    }

    public function getImagesForCarousel($user_id)
    {
        $statement =  "SELECT url FROM user_image WHERE user_id = ? ORDER BY isactive DESC";
        $attributes = array($user_id);
        $req = $this->pdo->prepare($statement);
        $req->execute($attributes);
        $rep = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rep as $key => $value) {
            $temp = $value['url'];
            unset($rep[$key]['url']);
            $rep[$key] = $temp;
        }
        return $rep;
    }

    public function getProfilePictureUrl($user_id)
    {
        $statement =  "SELECT * FROM user_image WHERE user_id = ? AND isactive = 1";
        $attributes = array($user_id);
        $req = $this->pdo->prepare($statement);
        $rep = $req->execute($attributes);
        if ($ret = $req->fetch(PDO::FETCH_ASSOC))
            return $ret['url'];
        //picture per default
        return "./assets/img/user.png";
    }

    public function getUserImageActive($user_id)
    {
        $statement =  "SELECT * FROM user_image WHERE user_id = ? AND isactive = 1";
        $attributes = array($user_id);
        $req = $this->pdo->prepare($statement);
        $rep = $this->execute($statement, $attributes, __CLASS__, true);
        if ($rep === false)
        {
            $rep = new self();
            $rep->setUrl("/assets/img/user.png");
        }
        return $rep;
    }

    private function desactivateOldProfilePic($user_id)
    {
        $statement =  "UPDATE user_image SET isactive = 0 WHERE user_id = ? AND isactive = 1";
        $attributes = array($user_id);
        $req = $this->pdo->prepare($statement);
        $rep = $req->execute($attributes);
    }

    private function activateNewProfilePic($user_id, $position)
    {
        $statement =  "UPDATE user_image SET isactive = 1 WHERE user_id = ? AND position = ?";
        $attributes = array($user_id, $position);
        $req = $this->pdo->prepare($statement);
        $rep = $req->execute($attributes);
    }

    private function imageExists($user_id, $pic_num)
    {
        $statement =  "SELECT * FROM user_image WHERE user_id = ? AND position = ?";
        $attributes = array($user_id, $pic_num);
        $req = $this->pdo->prepare($statement);
        $rep = $req->execute($attributes);
        if ($ret = $req->fetch(PDO::FETCH_ASSOC))
            return $ret;
        return false;
    }

    private function getImageUrl($user_id, $position)
    {
        $statement =  "SELECT * FROM user_image WHERE user_id = ? AND position = ?";
        $attributes = array($user_id, $position);
        $req = $this->pdo->prepare($statement);
        $rep = $req->execute($attributes);
        if ($ret = $req->fetch(PDO::FETCH_ASSOC))
            return $ret['url'];
        return false;
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
     * Get the value of Url
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of Url
     *
     * @param mixed url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

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

}