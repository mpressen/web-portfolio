<?php

require_once("../model/shared/user_id_from_login.php");

function toggle_flip_status($picture_id, $user_id)
{
    global $DB;
    $prep = $DB->prepare('UPDATE pictures SET flip = IF(flip = 1, 0, 1) WHERE picture_id = ? AND user_id = ?');
    $prep->execute(array($picture_id, $user_id));
}

?>