<?php
session_start();

require __DIR__ . DS . '..'. DS . 'vendor' . DS . 'autoload.php';

date_default_timezone_set('Europe/Paris');

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true,
		'determineRouteBeforeAppMiddleware' => true,
		'routerCacheFile' => false
	],
]);

require __DIR__ . DS . 'container.php';

require __DIR__ . DS . '..'. DS . 'app' . DS . 'routes.php';