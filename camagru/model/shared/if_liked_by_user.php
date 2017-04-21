<?php

function if_liked_by_user($picture_id, $user_id)
{
    global $DB;
    $prep = $DB->prepare('SELECT * FROM likes WHERE picture_id = ? AND user_id = ?');
    $prep->execute(array($picture_id, $user_id));
    $ret = $prep->fetch();
    return ($ret);
}

?>