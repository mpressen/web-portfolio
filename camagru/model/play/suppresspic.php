<?php

require_once("../model/shared/user_id_from_login.php");

function delete_picture($picture_id, $user_id)
{
    global $DB;
    $prep = $DB->prepare('DELETE FROM pictures WHERE picture_id = ? AND user_id = ?');
    $prep->execute(array($picture_id, $user_id));
    unlink('../pictures/'.$picture_id);
}

function get_my_next_pictures($user_id, $last_picture_id)
{
    global $DB;
    $prep = $DB->prepare('SELECT * FROM pictures WHERE user_id = ? AND picture_id < ? ORDER BY picture_id DESC');
    $prep->execute(array($user_id, $last_picture_id));
    $ret = $prep->fetchAll(PDO::FETCH_ASSOC);
    return ($ret);
}

?>