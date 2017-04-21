<?php
namespace App\Middleware;

use App\Models\User;
use App\Models\Blacklist;
use App\Models\Notification;
use App\Models\Likes;

class LikeMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		$next($request, $response);
		$user = $this->container->auth->user();
		$args = $request->getAttribute('routeInfo')[2];
		$status = $response->getStatusCode();
		$block = new Blacklist();
		if ($status === 200 && $args['id'] != $user->getId() && $block->isBlock($args['id'], $user->getId()) !== true)
		{
			$notif = new Notification();
			$link = $this->router->pathFor('user', ['id' => $user->getId()]);
			$likes = new Likes();
			$u = new User();
			if (($u = $u->getUserById($args['id']))){
				$u->setPopularity( intval($u->getPopularity()) + 3 );
				$u->update($u->getId(), 'popularity', $u->getPopularity());
			}
			if ($likes->getLikes($args['id'], $user->getId()))
				$notif->addNotification("{$user->getUsername()} has liked your profile too, you can now tchat with", "like", $link, $args['id']);
			else
				$notif->addNotification("{$user->getUsername()} has liked your profile", "like", $link, $args['id']);
		}
		return $response;
	}
}