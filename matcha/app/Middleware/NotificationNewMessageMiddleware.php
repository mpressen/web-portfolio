<?php

namespace App\Middleware;
use App\Models\Notification;
use App\Models\Blacklist;

/**
*
*/
class NotificationNewMessageMiddleware extends Middleware
{

	public function __invoke($request, $response, $next)
	{
		$response = $next($request,$response);
		$param = $this->sanitize->special_chars($request);
		$block = new Blacklist();
		$status = $response->getStatusCode();
		$user = $this->auth->user();
		if ($status === 200 && $param['dest_id'] != $user->getId() && $block->isBlock($param['dest_id'], $user->getId()) !== true){
			$notif = new Notification();
			$link = $this->router->pathFor('chat.index', ['chat_id' => $param['chat_id']]);
			$message ="{$user->getUsername()} sent you a new message.";
			$notif->addNotification($message, "message", $link, $param['dest_id']);
		}
		return $response;
	}
}