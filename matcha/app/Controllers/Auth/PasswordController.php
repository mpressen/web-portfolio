<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use App\Mail\Mail;

class PasswordController extends Controller
{
	public function getChangePassword($request,$response)
	{
		return $this->view->render($response,'auth/password/change.twig');
	}

	public function postChangePassword($request,$response)
	{
		$data = $this->sanitize->special_chars($request);
		$user = $this->auth->user();
		if (password_verify($data['currentpassword'], $user->getPassword()) === false)
		{
			$this->flash->addMessage('error','Your current password isn\'t valid');
			$bool = false;
		}
		if ($this->validator::validation($data) === false)
			$bool = false;
		if (isset($bool) && $bool === false)
			return $response->withRedirect($this->router->pathFor('auth.password.change'));
		$user->update($user->getId(), 'password', password_hash($data["password"], PASSWORD_DEFAULT));
		$this->flash->addMessage('info','Your password has been changed');
		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getAskResetPassword($request,$response)
	{
		return $this->view->render($response,'auth/password/askreset.twig');
	}

	public function postAskResetPassword($request,$response)
	{
		$data = $this->sanitize->special_chars($request);
		$user = new User($data);
		$user = $user->getUserByEmail($user->getEmail());
		if ($user !== false)
		{
			$path = 'http://'.$_SERVER["HTTP_HOST"].$this->router->pathFor('auth.password.reset', ['id' => urlencode($user->getId()), 'token' => urlencode($user->getToken())]);
			$mail = new Mail();
	        $message =  "Hello " . $user->getUsername() . ",<br/><br/>";
	        $message .= "To reset your password, click on the link below (or copy and paste the URL into your browser):<br/>";
	        $message .= "<a href='".$path."'>reset my password</a><br/><br/>";
	        $message .= "---------------<br/>Please do not reply to this message.";
	        $mail->setTo($user->getEmail());
	        $mail->setSubject("Reset your password");
	        $mail->setMessage($message);
	        $headers = "Content-Type: text/html; charset=UTF-8\r\n";
	        $headers .= "From: password@matcha.fr";
	        $mail->setHeaders($headers);
			if ($mail->send())
				$this->flash->addMessage('info','An email has been sent	to your mailbox to confirm the change of your password');
			else
				$this->flash->addMessage('error','An error has occurred, please try again later');
		}
		else
			$this->flash->addMessage('error','Your email doesn\'t exist in our database.');

		return $response->withRedirect($this->router->pathFor('auth.password.askreset'));
	}

	public function getResetPassword($request,$response,$args)
	{
		$id = urldecode($args['id']);
		$token = urldecode($args['token']);
		$user = new User();
		$user = $user->getUserById($id);
		$test = true;
		if ($user === false)
		{
			$this->flash->addMessage('error','No account were found');
			$test = false;
		}
		else if($user->getToken() !== $token)
		{
			$this->flash->addMessage('error','Link is not valid. <a href="'.$this->router->pathFor('auth.password.askreset').'">Please repeat the request for reinitialization of your password.</a>');
			$test = false;
		}
		if ($test === true)
			return $this->view->render($response,'auth/password/reset.twig', array('id' => $args['id'], 'token' => $args['token']));
		else
			return $response->withRedirect($this->router->pathfor('auth.password.askreset'));
	}

	public function postResetPassword($request,$response)
	{
		$data = $this->sanitize->special_chars($request);
		$id = urldecode($data['id']);
		$token = urldecode($data['token']);
		if ($this->validator::validation($data) === true)
		{
			$user = new User();
			$user = $user->getUserById($id);
			$test = true;
			if ($user === false)
			{
				$this->flash->addMessage('error','No account were found. you can signup <a href="'.$this->router->pathFor('auth.signup').'">here.</a>');
				$test = false;
			}
			else if($user->getToken() !== $token)
			{
				$this->flash->addMessage('error','Link is not valid. Please restart the request for reinitialization of your password.</a>');
				$test = false;
			}
			if ($test === false)
				return $response->withRedirect($this->router->pathfor('auth.password.askreset'));
			else
			{
				$user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT));
				$user->generateToken();
				$user->update($user->getId(), 'password', $user->getPassword());
				$user->update($user->getId(), 'token', $user->getToken());
				$this->flash->addMessage('success','Your password has been changed.');
				return $response->withRedirect($this->router->pathfor('auth.signin'));
			}
		}
		else
			return $response->withRedirect($this->router->pathfor('auth.password.reset', array('id' => $data['id'], 'token' => $data['token'])));
	}
}