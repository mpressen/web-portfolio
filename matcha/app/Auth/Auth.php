<?php
namespace App\Auth;

use App\Models\User;

class Auth
{
	public function user()
	{
		if (empty($_SESSION['user']))
			return false;
		$user = new User();
		return $user->getUserById($_SESSION['user']);
	}

	public function check()
	{
		return (!empty($_SESSION['user']) && !empty($_SESSION['loggued_on_user']));
	}

	public function attempt($username, $password)
	{
		$user = new User();
		$user = $user->getUserByUsername($username);
		if ($user !== false && password_verify($password, $user->getPassword()))
		{
			$_SESSION['user'] = $user->getId();
			$_SESSION['loggued_on_user'] = true;
			return true;
		}
		return false;
	}

	public function logout()
	{
		$user = $this->user();
		$user->update($user->getId(), 'isconnected', '0');
		unset($_SESSION['user']);
		unset($_SESSION['loggued_on_user']);
	}
}