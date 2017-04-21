<?

require_once("../../model/shared/user_from_login_and_confirmkey.php");

function confirm_user($login)
{
	global $DB;
	$prep = $DB->prepare('UPDATE users SET confirmation ="1" WHERE login = ?');
	$prep->execute(array($login));
}

function update_pwd($login, $new_pwd)
{
	global $DB;
	$prep = $DB->prepare('UPDATE users SET pwd = ? WHERE login = ?');
	$prep->execute(array(hash(whirlpool, $new_pwd), $login));
}

?>