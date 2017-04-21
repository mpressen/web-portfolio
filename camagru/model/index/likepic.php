<?php

require_once("../model/shared/user_id_from_login.php");
require_once("../model/shared/if_liked_by_user.php");

function delete_user_like($picture_id, $user_id)
{
    global $DB;
    $prep = $DB->prepare('DELETE FROM likes WHERE picture_id = ? AND user_id = ?');
    $prep->execute(array($picture_id, $user_id));
}

function add_user_like($user_id, $picture_id)
{
    global $DB;
    $prep = $DB->prepare('INSERT INTO likes(user_id, picture_id) VALUES(?, ?)');
    $prep->execute(array($user_id, $picture_id));
}

?>