<?

function picture_from_picture_id($picture_id)
{
    global $DB;
    $prep = $DB->prepare('SELECT * FROM pictures WHERE picture_id = ?');
    $prep->execute(array($picture_id));
    $ret = $prep->fetch();
    return ($ret);
}

?>