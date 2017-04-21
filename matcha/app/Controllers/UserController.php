<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\UserImage;
use App\Models\UserHasTag;
use App\Models\Visited;
use App\Models\Report;
use App\Models\Blacklist;
use App\Models\Likes;
use App\Models\Chat;
use App\Models\ChatUser;

class UserController extends Controller
{
	public function index( $request , $response , $args )
	{
		$u = new User();
		if (($user = $u->getUserByIdWithTags( $args['id'] )) !== false)
		{
			$user_loggued = $this->container->auth->user();
			$user_loggued_tags = [];
			$tags = new UserHasTag();
			$tags = $tags->getAllTagsByUserId($user_loggued->getId());
			foreach ($tags as $o)
				$user_loggued_tags[] = $o->getName();

			$user_image = new UserImage();

			$user_loggued_image = $user_image->getImagesForCarousel( $user_loggued->getId() );

			$user_image = $user_image->getImagesForCarousel( $args['id'] );

			$usertags = [];
			foreach ($user->getTags() as $o)
				$usertags[] = $o->name;

			$blacklist = new Blacklist();
			$blacklist = $blacklist->getBlacklist($user_loggued->getId(), $args['id'] );

			$report = new Report();
			$report = $report->getReport($user_loggued->getId(), $args['id'] );

			$likes = new Likes();
			$logguedUserIsLiked = $likes->checkIfThisUserLikesMe($user_loggued->getId(), $args['id'] );
			$likelolol = $likes->getLikes($user_loggued->getId(), $args['id'] );

			return $this->view->render( $response, 'user.twig', ['user' => $user, 'usertags' => $usertags, 'userimage' => $user_image, 'visitor_image' => $user_loggued_image, 'visitor_tags' => $user_loggued_tags, 'blacklist' => $blacklist, 'report' => $report, 'likes' => $likelolol, 'user_loggued' => $user_loggued,
				'loggued_user_is_liked' => $logguedUserIsLiked]);
		}
		return $response->withStatus(404);
	}

	public function like( $request , $response , $args )
	{
		$user_loggued = $this->container->auth->user();
		$likes = new Likes();
		if ($user_loggued->getId() != $args['id'] && $user_loggued->idExists($args['id'])){
			if ($likes->getLikes($args['id'], $user_loggued->getId())){
				$chat = new Chat();
				$chatId = $chat->insert();
				$chatUser = new ChatUser();
				$chatUser->insert($chatId, $user_loggued->getId());
				$chatUser->insert($chatId, $args['id']);
			}
			$likes->setSrcUserId( $user_loggued->getId() )->setDestUserId( $args['id'] )->insert();
		}

	}

	public function unlike( $request , $response , $args )
	{
		$likes = new Likes();
		$user_loggued = $this->container->auth->user();
		if (($likes = $likes->getLikes( $user_loggued->getId(), $args['id'] ))){
			if ($likes->getLikes($args['id'], $user_loggued->getId())){
				$chatUser = new ChatUser();
				$chatUser = $chatUser->getAllContact($user_loggued->getId());
				foreach ($chatUser as $key => $value) {
					if ($value['user']->getId() == $args['id']){
						$chat = new Chat();
						$chat->delete($value['chat_id']);
					}
				}
			}
			$likes->delete();
		}
	}

	public function report( $request , $response , $args )
	{
		$report = new Report();
		$user_loggued = $this->container->auth->user();
		if ($user_loggued->getId() != $args['id'] && $user_loggued->idExists($args['id'])){
			$report->setSrcUserId( $user_loggued->getId() )->setDestUserId( $args['id'] )->insert();
		}
	}

	public function block( $request , $response , $args )
	{
		$blacklist = new Blacklist();
		$user_loggued = $this->container->auth->user();
		if ($user_loggued->getId() != $args['id'] && $user_loggued->idExists($args['id'])){
			$blacklist->setSrcUserId( $user_loggued->getId() )->setDestUserId( $args['id'] )->insert();
			$likes = new Likes();
			if (($likes = $likes->getLikes( $user_loggued->getId(), $args['id'] ))){
				if ($likes->getLikes($args['id'], $user_loggued->getId())){
					$chatUser = new ChatUser();
					$chatUser = $chatUser->getAllContact($user_loggued->getId());
					foreach ($chatUser as $key => $value) {
						if ($value['user']->getId() == $args['id']){
							$chat = new Chat();
							$chat->delete($value['chat_id']);
						}
					}
				}
				$likes->delete();
			}
		}
	}

	public function unblock( $request , $response , $args )
	{
		$blacklist = new Blacklist();
		$user_loggued = $this->container->auth->user();
		$blacklist->setSrcUserId( $user_loggued->getId() )->setDestUserId( $args['id'] )->delete();
	}
}