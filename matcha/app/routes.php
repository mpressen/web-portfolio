<?php

use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\NotificationNewMessageMiddleware;
use App\Middleware\VisitedMiddleware;
use App\Middleware\LikeMiddleware;
use App\Middleware\DislikeMiddleware;

$app->add(new App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new App\Middleware\OldInputMiddleware($container));

$app->get('/','AuthController:getSignIn')->add($container->CsrfViewMiddleware)->add($container->csrf)->add(new GuestMiddleware($container));

$app->get('/home','HomeController:index')->add(new AuthMiddleware($container))->setName('home');
$app->get('/search','SearchController:index')->add(new AuthMiddleware($container))->setName('search');
$app->get('/user/{id}', 'UserController:index')->add(new VisitedMiddleware($container))->add(new AuthMiddleware($container))->setName('user');

$app->group('/auth/',function () {
	$this->get('signin','AuthController:getSignIn')->setName('auth.signin');
	$this->post('signin','AuthController:postSignIn');
	$this->get('signup','AuthController:getSignUp')->setName('auth.signup');
	$this->post('signup','AuthController:postSignUp');
	$this->post('presign42', 'AuthController:postpreSign42')->setName('auth.presign42');
	$this->get('sign42', 'AuthController:getSign42')->setName('auth.sign42');
	$this->post('sign42', 'AuthController:postSign42');
	$this->get('signupfb','AuthController:getpreSignFaceBook')->setName('auth.presignFB');
	$this->post('signFB', 'AuthController:postSignFB')->setName('auth.signFB');
})->add($container->CsrfViewMiddleware)->add($container->csrf)->add(new GuestMiddleware($container));
$app->get('/auth/signout','AuthController:getSignOut')->add(new AuthMiddleware($container))->setName('auth.signout');

$app->group('/user/{id:[0-9]+}/', function () use ($container) {
	$this->post('like','UserController:like')->setName('user.like')->add(new LikeMiddleware($container));

	$this->post('unlike','UserController:unlike')->setName('user.unlike')->add(new DislikeMiddleware($container));
	$this->post('report','UserController:report')->setName('user.report');
	$this->post('block','UserController:block')->setName('user.block');
	$this->post('unblock','UserController:unblock')->setName('user.unblock');
})->add(new AuthMiddleware($container));

$app->group('/auth/password',function () {
	$this->get('/askreset','PasswordController:getAskResetPassword')->setName('auth.password.askreset');
	$this->post('/askreset','PasswordController:postAskResetPassword');
	$this->get('/reset/{id:[0-9]+}/{token}','PasswordController:getResetPassword')->setName('auth.password.reset');
	$this->post('/reset','PasswordController:postResetPassword')->setName('auth.password.post.reset');
})->add(new GuestMiddleware($container));

$app->group('/auth/password',function () {
	$this->get('/change','PasswordController:getChangePassword')->setName('auth.password.change');
	$this->post('/change','PasswordController:postChangePassword');
})->add($container->CsrfViewMiddleware)->add($container->csrf)->add(new AuthMiddleware($container));

$app->group('/profile',function () {
	$this->get('','ProfileController:getProfile')->setName('profile');
	$this->post('/email/update','ProfileController:ajaxEmailUpdate');
	$this->post('/firstname/update','ProfileController:ajaxFirstnameUpdate');
	$this->post('/lastname/update','ProfileController:ajaxLastnameUpdate');
	$this->post('/age/update','ProfileController:ajaxAgeUpdate');
	$this->post('/gender/update','ProfileController:ajaxGenderUpdate');
	$this->post('/attraction/update','ProfileController:ajaxAttractionUpdate');
	$this->post('/bio/update','ProfileController:ajaxBioUpdate');
	$this->post('/tag/new','ProfileController:ajaxTagNew');
	$this->post('/tag/update','ProfileController:ajaxTagUpdate');
	$this->post('/tag/delete','ProfileController:ajaxTagDelete');
	$this->get('/location/locate','LocationController:getLocateMe');
	$this->post('/location/update','LocationController:postLocateUpdate');
	$this->post('/img/add','ImageController:postImgAdd');
	$this->post('/img/del','ImageController:postImgDel');
	$this->post('/img/setProfile','ImageController:postImgProfileChange');
	$this->post('/img/getProfilePic','ImageController:postImgProfileGet');
})->add(new AuthMiddleware($container));

//historic
$app->group('/history', function() use ($container){
$this->get('','HistoricController:getHistoric')->setName('historic');
$this->post('/clear','HistoricController:clearAllHistoric');
$this->post('/delete_element','HistoricController:DeleteHistoryEntry');
})->add(new AuthMiddleware($container));

$app->group('/chat', function() use ($container) {
	$this->get('[/{chat_id:[0-9]+}]', 'ChatController:getChatIndex')->setName('chat.index');
	$this->get('/getContacts[/{chat_id:[0-9]+}]', 'ChatController:getContacts')->setName('chat.getContacts');
	$this->post('/send', 'ChatController:postChatSend')->add(new NotificationNewMessageMiddleware($container));
})->add(new AuthMiddleware($container));

$app->group('/notification', function(){
	$this->post('/getAllNotification', 'NotificationController:getAllNotification');
	$this->post('/markAsRead', 'NotificationController:postMarkAsRead');
	$this->post('/markAllAsRead', 'NotificationController:postMarkAllAsRead');
})->add(new AuthMiddleware($container));