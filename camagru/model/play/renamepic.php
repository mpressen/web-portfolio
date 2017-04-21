<?php

require_once("../model/shared/user_id_from_login.php");

function update_pic_title($name, $user_id, $picture_id)
{
    global $DB;
    $prep = $DB->prepare('UPDATE pictures SET title = ? WHERE user_id = ? AND picture_id = ?');
    $prep->execute(array($name, $user_id, $picture_id));
}

?>