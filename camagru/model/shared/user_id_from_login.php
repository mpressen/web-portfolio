<?php

function user_id_from_login($login)
{
		global $DB;
		if ($login)
		{
			$user_id = $DB->prepare('SELECT user_id FROM users WHERE login = ?');
			$user_id->execute(array($login));
			return ($user_id->fetch()[0]);
		}
		return (NULL);
}