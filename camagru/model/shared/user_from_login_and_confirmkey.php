<?

function user_from_login_and_confirmkey($login, $confirmkey)
{
    global $DB;
    $prep = $DB->prepare('SELECT * FROM users WHERE login = ? AND confirmkey = ?');
    $prep->execute(array($login, $confirmkey));
    $ret = $prep->fetch();
    return ($ret);
}

?>