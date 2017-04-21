<?php
namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\UserImage;
use App\Location\Location;
use App\Controllers\Controller;
use Facebook\Facebook;

class AuthController extends Controller
{
	private function getFBLoginUrl($redirect_uri, $permissions = ['email', 'user_birthday', 'user_relationship_details'])
	{
		if (isset($_SESSION['OAUTH_FB']['user']))
			unset($_SESSION['OAUTH_FB']['user']);

		$fb = new Facebook(
		[
			'app_id' => $this->view->getEnvironment()->getGlobals()['OAUTH_FB_APP_ID'],
			'app_secret' => $this->view->getEnvironment()->getGlobals()['OAUTH_FB_APP_SECRET'],
  			'default_graph_version' => 'v2.8'
		]);

		$helper = $fb->getRedirectLoginHelper();

		return $helper->getLoginUrl($redirect_uri, $permissions);
	}

	public function getSignOut($request,$response)
	{
		$this->auth->logout();
		return $response->withRedirect($this->router->pathFor('auth.signin'));
	}

	public function getSignIn($request,$response)
	{
		$redirect_uri = $request->getUri()->getScheme().'://'.$request->getUri()->getHost().':'.$request->getUri()->getPort().$this->router->pathFor('auth.presignFB');
		return $this->view->render($response,'auth/signin.twig', ['fbloginurl' => $this->getFBLoginUrl($redirect_uri)]);
	}

	public function postSignIn($request,$response)
	{
		$data = $this->sanitize->special_chars($request);
		$auth = $this->auth->attempt($data['username'],$data['password']);

		if (!$auth)
		{
			$this->flash->addMessage('error','Could not sign you in with those details');
			return $response->withRedirect($this->router->pathFor('auth.signin'));
		}

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function getSignUp($request,$response)
	{
		$redirect_uri = $request->getUri()->getScheme().'://'.$request->getUri()->getHost().':'.$request->getUri()->getPort().$this->router->pathFor('auth.presignFB');
		return $this->view->render($response,'auth/signup.twig', ['fbloginurl' => $this->getFBLoginUrl($redirect_uri)]);
	}

	public function postSignUp($request,$response)
	{
		$data = $this->sanitize->special_chars($request);

		if (!$this->validator::validation($data))
			return $response->withRedirect($this->router->pathFor('auth.signup'));

		$user = new User($data);
		if (($boolMail = $user->emailExists($data['email'])))
			$this->flash->addMessage('error', 'Email already exist');
		if (($boolUser = $user->usernameExists($data['username'])))
			$this->flash->addMessage('error', 'Username already exist');
		if ($boolMail || $boolUser)
			return $response->withRedirect($this->router->pathFor('auth.signup'));

		$user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT));
		$user->generateToken();
		$location = new Location();
		if ($location->reverse_geocoding($data["latitude"], $data["longitude"]) !== false || $location->geolocationByIp() !== false)
		{
			$user->setLatitude($location->getLatitude());
	        $user->setLongitude($location->getLongitude());
			$user->setPostalCode($location->getPostalCode());
			$user->setLocality($location->getLocality());
			$user->setCountry($location->getCountry());
		}
		$user->insert();

		$this->flash->addMessage('success', 'You have been signed up');
		$this->flash->addMessage('success', 'You can complete your profile <a href="'.$this->router->pathFor('profile').'">here</a>');
		// $this->flash->addMessage('warning', 'If you don\'t complete your profile you won\'t be visible to other users !');
		$this->auth->attempt($user->getUsername(), $data['password']);

		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function postpreSign42($request, $response)
	{
		$data = $this->sanitize->special_chars($request);

		if (!$this->validator->validationLatitude($data['latitude']) || !$this->validator->validationLongitude($data['longitude']))
		{
			$this->flash->addMessage('error', 'An error occured, please retry.');
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}

		$redirect_uri = $request->getUri()->getScheme().'://'.$request->getUri()->getHost().':'.$request->getUri()->getPort().$this->router->pathFor('auth.sign42');

		$location = new Location();
		if ($location->reverse_geocoding($data["latitude"], $data["longitude"]) !== false || $location->geolocationByIp() !== false)
			$_SESSION['LOCATION'] = serialize($location);

		$_SESSION['state'] = uniqid();

		return $response->withRedirect("https://api.intra.42.fr/oauth/authorize?client_id=".$this->view->getEnvironment()->getGlobals()['OAUTH_42_CLIENT_ID']."&redirect_uri=".urlencode($redirect_uri)."&state=".$_SESSION['state']."&scope=public&response_type=code");
	}

	public function getSign42($request, $response)
	{
		$data = $this->sanitize->special_chars($request);

		if (!empty($data['code']))
		{
			$redirect_uri = $request->getUri()->getScheme().'://'.$request->getUri()->getHost().':'.$request->getUri()->getPort().$request->getUri()->getBasePath().$request->getUri()->getPath();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.intra.42.fr/oauth/token");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&client_id=".$this->view->getEnvironment()->getGlobals()['OAUTH_42_CLIENT_ID']."&client_secret=".$this->view->getEnvironment()->getGlobals()['OAUTH_42_SECRET']."&code=".$data['code']."&redirect_uri=".urlencode($redirect_uri)."&state=".$_SESSION['state']);

			$oauth_response = json_decode(curl_exec($ch), true);

			curl_close($ch);

			if (empty($oauth_response['access_token']))
			{
				$this->flash->addMessage('error','Could not retreive information from external source, please register using the form below.');
				return $response->withRedirect($this->router->pathFor('auth.signup'));
			}

			$_SESSION['OAUTH_42']['access_token'] = $oauth_response['access_token'];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.intra.42.fr/v2/me?access_token=".$oauth_response['access_token']);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$oauth_response = json_decode(curl_exec($ch), true);

			curl_close($ch);

			if (empty($oauth_response['id']))
			{
				$this->flash->addMessage('error', 'Could not retrieve information from external source, please register using the form below.');
				return $response->withRedirect($this->router->pathFor('auth.signup'));
			}

			$user = new User();
			if (($boolMail = $user->emailExists($oauth_response['email'])))
				$this->flash->addMessage('error', 'Email already exist');
			if (($boolUser = $user->usernameExists($oauth_response['login'])))
				$this->flash->addMessage('error', 'Username already exist');
			if ($boolMail || $boolUser)
				return $response->withRedirect($this->router->pathFor('auth.signup'));

			$_SESSION['OAUTH_42']['user'] = serialize($oauth_response);
		}

		if (!empty($_SESSION['OAUTH_42']))
		{
			return $this->view->render($response, 'oauth_42.twig', ['login' => $oauth_response['login']]);
		}
		else
		{
			$this->flash->addMessage('error','An error occured, please register using the form below.');
			return $response->withRedirect($this->router->pathFor('auth.signup'));
		}
	}

	public function postSign42($request, $response)
	{
		$data = $this->sanitize->special_chars($request);

		if (!empty($_SESSION['LOCATION']) && !empty($_SESSION['OAUTH_42']['user']))
		{
			if (!$this->validator->validationPassword($data['password']))
				return $response->withRedirect($this->router->pathFor('auth.sign42'));

			$oauth_response = unserialize($_SESSION['OAUTH_42']['user']);
			$user = new User();
			$user->setFirstname($oauth_response['first_name'])->setLastname($oauth_response['last_name'])->setUsername($oauth_response['login'])->setEmail($oauth_response['email']);
			$user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT));
			$user->generateToken();

			$location = unserialize($_SESSION['LOCATION']);

			$user->setLatitude($location->getLatitude());
	        $user->setLongitude($location->getLongitude());
			$user->setPostalCode($location->getPostalCode());
			$user->setLocality($location->getLocality());
			$user->setCountry($location->getCountry());

			unset($_SESSION['LOCATION']);

			$user->insert();

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $oauth_response['image_url']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

			if (($img = curl_exec($ch)))
			{
				$user = $user->getUserbyUsername($oauth_response['login']);
				$user_image = new UserImage();
				$file_name = basename($oauth_response['image_url']);
				$file_path = $user_image::UPLOAD_PATH.$user->getId().'/';
	    		@mkdir($file_path, 0777, 1);
				if (file_put_contents($file_path.$file_name, $img))
					$user_image->addImage("./".$file_path.$file_name, $user->getId(), 1);
			}
			curl_close($ch);
			unset($_SESSION['OAUTH_42']['user']);

			$this->flash->addMessage('success', 'You have been signed up');
			$this->flash->addMessage('success', 'You can complete your profile <a href="'.$this->router->pathFor('profile').'">here</a>');
			// $this->flash->addMessage('warning', 'If you don\'t complete your profile you won\'t be visible to other users !');
			$this->auth->attempt($user->getUsername(), $data['password']);

			return $response->withRedirect($this->router->pathFor('home'));
		}
		$this->flash->addMessage('error','An error occured, please register using the form below.');

		return $response->withRedirect($this->router->pathFor('auth.signup'));
	}

	public function getpreSignFacebook($request, $response)
	{
		if (empty($_SESSION['OAUTH_FB']['user']))
		{
			$fb = new Facebook(
			[
				'app_id' => $this->view->getEnvironment()->getGlobals()['OAUTH_FB_APP_ID'],
				'app_secret' => $this->view->getEnvironment()->getGlobals()['OAUTH_FB_APP_SECRET'],
	  			'default_graph_version' => 'v2.8'
			]);

			$helper = $fb->getRedirectLoginHelper();

			try
			{
				$accessToken = $helper->getAccessToken();
				if (!isset($accessToken) || $helper->getError())
					throw new \Exception();
				$oauth_response = $fb->get('/me?fields=id,first_name,last_name,birthday,gender,interested_in,picture.width(720),email', $accessToken->getValue());
			}
			catch (\Exception $e)
			{
				$this->flash->addMessage('error', 'An error occured, please register using the form below.');
				return $response->withRedirect($this->router->pathFor('auth.signup'));
			}

			$_SESSION['needed'] = [];

			$fb_user = $oauth_response->getGraphUser();

			$user = new User();
			if (isset($fb_user['email']))
			{
				if ($user->emailExists($fb_user['email']))
				{
					$this->flash->addMessage('error', 'Email already exist');
					return $response->withRedirect($this->router->pathFor('auth.signup'));
				}
			}
			else
				$_SESSION['needed'] = 'email';

			$location = new Location();
			if ($location->reverse_geocoding($data["latitude"], $data["longitude"]) !== false || $location->geolocationByIp() !== false)
				$_SESSION['LOCATION'] = serialize($location);

			$_SESSION['OAUTH_FB']['user'] = serialize($fb_user);
		}

		return $this->view->render($response, 'oauth_fb.twig', ['user_name' => $fb_user['first_name']." ".$fb_user['last_name'], 'needed' => $_SESSION['needed']]);
	}

	public function postSignFB($request, $response)
	{
		if (!empty($_SESSION['OAUTH_FB']['user']))
		{
			$oauth_response = unserialize($_SESSION['OAUTH_FB']['user']);
			$data = $this->sanitize->special_chars($request);

			if (!$this->validator::validation($data))
				return $response->withRedirect($this->router->pathFor('auth.presignFB'));

			$user = new User();
			if ($user->usernameExists($data['login']))
			{
				$this->flash->addMessage('error', 'Username already exist');
				return $response->withRedirect($this->router->pathFor('auth.presignFB'));
			}
 
			if (!empty($oauth_response['first_name']))
				$user->setFirstname($oauth_response['first_name']);
			if (!empty($oauth_response['last_name']))
				$user->setLastname($oauth_response['last_name']);
			$user->setUsername($data['login']);
			if (in_array('email', $_SESSION['needed']))
			{
				if ($user->emailExists($data['email']))
				{
					$this->flash->addMessage('error', 'Email already exist');
					return $response->withRedirect($this->router->pathFor('auth.signup'));
				}
			}
			else
				$user->setEmail($oauth_response['email']);

			if (isset($oauth_response['gender']))
			{
				$user->setGender($oauth_response['gender']);
				if (isset($oauth_response['interested_in']) && (sizeof($oauth_response['interested_in']) != 2))
				{
					if ($user->getGender() == $oauth_response['interested_in'][0])
						$user->setAttraction('homosexual');
					else
						$user->setAttraction('heterosexual');
				}
			}
			if (isset($oauth_response['birthday']->date))
			{
				$date1 = new \DateTime();
				$date2 = new \DateTime($oauth_response['birthday']->date);
				$user->setAge($date1->diff($date2, true)->y);
			}

			unset($_SESSION['needed']);

			$user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT));
			$user->generateToken();

			$location = unserialize($_SESSION['LOCATION']);

			$user->setLatitude($location->getLatitude());
	        $user->setLongitude($location->getLongitude());
			$user->setPostalCode($location->getPostalCode());
			$user->setLocality($location->getLocality());
			$user->setCountry($location->getCountry());

			$user->insert();

			if ($oauth_response['picture'] && !$oauth_response['picture']['is_silhouette'])
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $oauth_response['picture']['url']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

				if (($img = curl_exec($ch)))
				{
					$user = $user->getUserbyUsername($data['login']);
					$user_image = new UserImage();
					$file_name = uniqid().'.jpg';
					$file_path = $user_image::UPLOAD_PATH.$user->getId().'/';
		    		@mkdir($file_path, 0777, 1);
					if (file_put_contents($file_path.$file_name, $img))
						$user_image->addImage("./".$file_path.$file_name, $user->getId(), 1);
				}
				curl_close($ch);
			}

			unset($_SESSION['LOCATION']);

			$this->flash->addMessage('success', 'You have been signed up');
			$this->flash->addMessage('success', 'You can complete your profile <a href="'.$this->router->pathFor('profile').'">here</a>');
			// $this->flash->addMessage('warning', 'If you don\'t complete your profile you won\'t be visible to other users !');
			$this->auth->attempt($user->getUsername(), $data['password']);

			return $response->withRedirect($this->router->pathFor('home'));
		}
	}
}