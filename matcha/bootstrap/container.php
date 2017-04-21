<?php

$container = $app->getContainer();

$container['auth'] = function($container) {
	return new \App\Auth\Auth;
};

$container['flash'] = function($container) {
	return new \Slim\Flash\Messages;
};

$container['view'] = function ($container) {
	$view = new \Slim\Views\Twig(__DIR__ . '/../resources/views/', [
		'cache' => false,
		'debug' => true,
		]);

	$view->addExtension(new \Twig_Extension_Debug());
	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
		));

	$view->getEnvironment()->addGlobal('auth',[
		'check' => $container->auth->check(),
		'user' => $container->auth->user()
		]);
	$view->getEnvironment()->addGlobal('flash', $container->flash);

// 42 Oauth
	$view->getEnvironment()->addGlobal('OAUTH_42_CLIENT_ID', '7079c370dd642aa5166be4202499c3334177872b7c792cd4daa3fa5224b67f3b');

	$view->getEnvironment()->addGlobal('OAUTH_42_SECRET','eacd498bacb0ae3bf72b22b9471b1d36221fa03bd2c863baae4d68f486d2b5a0');

// FB Oauth
	$view->getEnvironment()->addGlobal('OAUTH_FB_APP_ID', '697366577108902');

	$view->getEnvironment()->addGlobal('OAUTH_FB_APP_SECRET', '38ff702d61be320a6270414327d5b7c2');

//Google Oauth	
	$view->getEnvironment()->addGlobal('OAUTH_GOOGLE_CLIENT_ID', '174033139812-msi08plcr5b3emhj43a3m8krjs432tni');

	return $view;
};

$container['notFoundHandler'] = function ($container) {
	return function ($request, $response) use ($container) {
		$message = "Page not found. Go to <a href='" . $container->router->pathFor('home'). "'>home</a>";
		return($container->view->render($response->withStatus(404), 'error.twig',['message' => $message]));
	};
};

$container['notAllowedHandler'] = function ($container) {
	return function ($request, $response, $methods) use ($container) {
		$message = 'Method must be one of: ' . implode(', ', $methods);
		return($container->view->render($response->withStatus(405), 'error.twig',['message' => $message]));
	};
};

$container['phpErrorHandler'] = function ($container) {
	return function ($request, $response) use ($container) {
		$message = "A error occured. Go to <a href='" . $container->router->pathFor('home'). "'>home</a>";
		return($container->view->render($response->withStatus(500), 'error.twig',['message' => $message]));
	};
};

$container['sanitize'] = function ($container){
	return new App\Sanitize\Sanitize;
};

$container['HomeController'] = function($container) {
	return new \App\Controllers\HomeController($container);
};

$container['SearchController'] = function($container) {
	return new \App\Controllers\SearchController($container);
};

$container['AuthController'] = function($container) {
	return new \App\Controllers\Auth\AuthController($container);
};

$container['PasswordController'] = function($container) {
	return new \App\Controllers\Auth\PasswordController($container);
};

$container['ProfileController'] = function($container) {
	return new \App\Controllers\Profile\ProfileController($container);
};

$container['HistoricController'] = function($container) {
	return new \App\Controllers\HistoricController($container);
};

$container['UserController'] = function($container) {
	return new \App\Controllers\UserController($container);
};

$container['LocationController'] = function($container) {
	return new \App\Controllers\Profile\LocationController($container);
};

$container['ImageController'] = function($container) {
	return new \App\Controllers\Profile\ImageController($container);
};

$container['ChatController'] = function($container) {
	return new \App\Controllers\ChatController($container);
};

$container['NotificationController'] = function($container) {
	return new \App\Controllers\NotificationController($container);
};

$container['csrf'] = function($container) {
	return new \Slim\Csrf\Guard;
};

$container['CsrfViewMiddleware'] = function($container) {
	return new \App\Middleware\CsrfViewMiddleware($container);
};

$container['validator'] = function($container) {
	return new \App\Validation\Validator($container);
};

$container['location'] = function($container) {
	return new \App\Location\Location;
};
