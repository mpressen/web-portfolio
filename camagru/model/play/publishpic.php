<?php

require_once("../model/shared/user_id_from_login.php");

function toggle_publish_status($picture_id, $user_id)
{
    global $DB;
    $prep = $DB->prepare('UPDATE pictures SET publish = IF(publish = 1, 0, 1) WHERE picture_id = ? AND user_id = ?');
    $prep->execute(array($picture_id, $user_id));
}

?>