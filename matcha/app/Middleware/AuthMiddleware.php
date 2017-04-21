<?php

namespace App\Middleware;
use App\Models\User as User;

/**
*
*/
class AuthMiddleware extends Middleware
{

	public function __invoke($request, $response, $next)
	{
		if(!$this->container->auth->check())
		{
			$this->container->flash->addMessage('error','Please sign in before doing that');
			if ($request->isXhr()){
				return $this->response->withJson(array('message' => 'no loggued'), 401);
			}
			return $response->withRedirect($this->container->router->pathFor('auth.signin'));
		}
		else
		{
			$user = $this->container->auth->user();
			$date = new \DateTime();
			$user->update($user->getId(), 'lastactivity', $date->format('Y-m-d H:i:s'));
			$user->update($user->getId(), 'isconnected', '1');
		}
		$response = $next($request,$response);
		return $response;
	}
}