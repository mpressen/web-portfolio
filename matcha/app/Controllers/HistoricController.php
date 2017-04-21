<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\ChatUser;
use App\Models\Visited;
use App\Models\Blacklist;
use App\Models\Notification;
use App\Models\UserImage;
use App\Controllers\Controller;
use App\Pagination\Pagination;
use App\Validation\Validator;

class HistoricController extends Controller
{
	public function getHistoric($request,$response)
	{
		$user_loggued = $this->container->auth->user();
		$user = new User();
		$user_represented = new User();
		$user = $user->getUserByIdWithTags($user_loggued->getId());

		//visits
		$visited = new Visited();

		$visited_profiles = $visited->getAllVisitedBySrcUserId($user->getId());

		$profile_pic_urls = [];
		$image_model = new UserImage();
		foreach($visited_profiles as $entry)
		{
			$profile_pic_urls[$entry['dest_user_id']] = $image_model->getProfilePictureUrl($entry['dest_user_id']);
		}

		//notifications
		$chat_user = new ChatUser();
		$notifications_user = new Notification();

		$notifications = $notifications_user->getAllNotificationsByUserId($user->getId());

		$profile_pic_urls2 = [];
		foreach($notifications as $entry)
		{
			$username = explode(" ", $entry['message'])[0];	
			if ($entry['type'] != 'message')
				$ref_id = substr($entry['link'], 6);
			else
			{
				$link_user = $user->getUserByUsername($username);
				$ref_id = $link_user->getId();
			}
			$profile_pic_urls2[$username] = $image_model->getProfilePictureUrl($ref_id);
		}

		//blocked_users
		$blacklist = new Blacklist();
		$blacklist = $blacklist->getAllBlackListBySrcUserId($user_loggued->getId());

		$blocked_users = [];
		$i = 0;
		foreach ($blacklist as $entry)
		{
			$blocked_users[$i]['username'] = $user_represented->getUserById($entry->getDestUserId())->getUsername();
			$blocked_users[$i]['profile_pic'] = $image_model->getProfilePictureUrl($entry->getDestUserId());
			$blocked_users[$i]['id'] = $entry->getDestUserId();
			$blocked_users[$i]['link'] = "/user/" . $entry->getDestUserId();
			$i++;
		}

		return $this->view->render($response, 'historic.twig', array(
			'user' => $user,
			'visited_profiles' => $visited_profiles,
			'notifs' => $notifications,
			'profile_pic_urls' => $profile_pic_urls,
			'profile_pic_urls2' => $profile_pic_urls2,
			'blocked_users' => $blocked_users
			));
}
public function clearAllHistoric($request,$response)
{
	if ($request->isXhr())
	{
		$type = $request->getParam('type');
		$user_loggued = $this->container->auth->user();
		$user = new User();
		$user = $user->getUserByIdWithTags($user_loggued->getId());
		if ($type == 'visits')
		{
			$visited = new Visited();
			$visited->clearHistoryOfUser($user->getId());
		}
		else if ($type == 'notifs')
		{
			$notifications = new Notification();
			$notifications->clearNotificationsOfUser($user->getId());
		}
		else if ($type == 'blocked_users')
		{
			$blacklist = new Blacklist();
			$blacklist->clearBlacklistOfUser($user->getId());
		}
	}
}

public function DeleteHistoryEntry($request,$response)
{
	if ($request->isXhr())
	{
		$id = $request->getParam('id');
		$type = $request->getParam('type');

		$user_loggued = $this->container->auth->user();
		$user_id = $user_loggued->getId();

		if ($type == 'close visits')
		{
			$visited = new Visited();
			$user_id_entry = $visited->getUserIdfromVisitedEntry($id);
			if ($this->validator->SecureProperId($user_id_entry, $user_id))
				$visited->clearHistoryEntry($id);
			}
			else if ($type == 'close notifs')
			{
				$notifications = new Notification();
				$user_id_notif = $notifications->getUserIdfromNotificationEntry($id);
				if ($this->validator->SecureProperId($user_id_notif, $user_id))
					$notifications->clearNotificationsEntry($id);
				}
				else if ($type == 'close blocked_users')
				{
					$blacklist = new Blacklist();
					$blacklist->getBlacklist($user_id, $id)->delete();
				}
			}
		}
	}