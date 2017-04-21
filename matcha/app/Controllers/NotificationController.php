<?php

namespace App\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function getAllNotification($request, $response)
    {
        if ($request->isXhr())
        {
            $args = $this->sanitize->special_chars($request);
            $notif = new Notification();
            $vars['notifications'] = $notif->getNotificationsByUserId($this->auth->user()->getId());
            $currents = json_decode($args['list'], true);
            if (empty($currents) && empty($vars['notifications']) ) {
                $rep = array('message' => 'no-modified');
                return $response->withJson($rep, 304);
            }
            else if (empty($currents) || empty($vars['notifications'])){
                return $this->view->render($response, 'templates/partials/notification.twig', $vars);
            }
            else if (is_array($currents))
            {
                foreach ($vars['notifications'] as $key => $value)
                {
                    if ($value->getId() != $currents[$key])
                        return $this->view->render($response, 'templates/partials/notification.twig', $vars);
                }
                $rep = array('message' => 'no-modified');
                return $response->withJson($rep, 304);
            }
            return $this->view->render($response, 'templates/partials/notification.twig', $vars);
        }
    }

    public function postMarkAsRead($request, $response)
    {
        if ($request->isXhr())
        {
            $args['id'] = $request->getParam('id');
            if (($id = filter_var($args['id'], FILTER_VALIDATE_INT)) !== false){
                $notif = new Notification();
                $notif = $notif->getNotificationById($id);
                if (!empty($notif) && $notif->getUserId() == $this->auth->user()->getId())
                    $notif->update($notif->getId(), 'isread', '1');
            }
        }
        return $response;
    }

    public function postMarkAllAsRead($request, $response)
    {
        if ($request->isXhr())
        {
            $notif = new Notification();
            $notif = $notif->getNotificationsByUserId($this->auth->user()->getId());
            foreach ($notif as $key => $value) {
                $value->update($value->getId(), 'isread', '1');
            }
            return $response;
        }
    }

}