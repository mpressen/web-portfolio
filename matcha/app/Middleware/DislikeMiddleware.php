<?php
namespace App\Middleware;

use App\Models\User;
use App\Models\Blacklist;
use App\Models\Notification;

class DislikeMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		$next($request, $response);
		$user = $this->container->auth->user();
		$args = $request->getAttribute('routeInfo')[2];
		$status = $response->getStatusCode();
		$block = new Blacklist();
		$user = $this->auth->user();
		if ($status === 200 && $args['id'] != $user->getId() && $block->isBlock($args['id'], $user->getId()) !== true){
			$notif = new Notification();
			$link = $this->router->pathFor('user', ['id' => $user->getId()]);
			$notif->addNotification("{$user->getUsername()} has disliked your profile", "nomatch", $link, $args['id']);
		}
		return $response;
	}
}