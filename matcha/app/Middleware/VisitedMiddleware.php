<?php
namespace App\Middleware;

use App\Models\User;
use App\Models\Visited;
use App\Models\Blacklist;
use App\Models\Notification;

class VisitedMiddleware extends Middleware
{
	public function __invoke($request, $response, $next)
	{
		$next($request, $response);
		$args = $request->getAttribute('routeInfo')[2];
		$status = $response->getStatusCode();
		$block = new Blacklist();
		$visited = new Visited();
		$user = $this->auth->user();
		if ($status === 200 && $args['id'] != $user->getId() && $block->isBlock($args['id'], $user->getId()) !== true){
			$visited->setSrcUserId($user->getId())->setDestUserId($args['id'])->insert();
			$notif = new Notification();
			$u = new User();
			if (($u = $u->getUserById($args['id']))){
				$u->setPopularity( intval($u->getPopularity()) + 1 );
				$u->update($u->getId(), 'popularity', $u->getPopularity());
			}
			$link = $this->router->pathFor('user', ['id' => $user->getId()]);
			$notif->addNotification("{$user->getUsername()} has visited your profile.", "visit", $link, $args['id']);
		}
		return $response;
	}
}