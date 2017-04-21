<?
require_once("../model/shared/user_id_from_login.php");

function add_new_picture($user_id, $flip)
{
    global $DB;
    $prep = $DB->prepare('INSERT INTO pictures(user_id, flip) VALUES(?, ?)');
    $prep->execute(array($user_id, $flip));
}

function get_last_picture_id($user_id)
{
    global $DB;
    $prep = $DB->prepare('SELECT picture_id FROM pictures WHERE user_id = ? ORDER BY picture_id DESC');
    $prep->execute(array($user_id));
    $ret = $prep->fetch()[0];
    return ($ret);
}

?>