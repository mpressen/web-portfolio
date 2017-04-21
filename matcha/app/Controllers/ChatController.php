<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserImage;
use App\Models\ChatUser;
use App\Models\ChatMessage;

class ChatController extends Controller
{

    function getChatIndex($request, $response, $args)
    {
        $user = $this->auth->user();
        $chat = new ChatUser();
        $messages = new ChatMessage();
        $vars['contacts'] = $chat->getAllContact($user->getId());
        if (!empty($args['chat_id'])){
            if ($chat->chatIdAndUserIdExist($args['chat_id'], $user->getId()) === false)
                return $this->response->withRedirect($this->router->pathFor('chat.index'));
            else{
                $vars['conversation'] = $messages->getConversationByChatId($args['chat_id']);
                $image  = new UserImage();
                foreach ($vars['conversation'] as $key => $value) {
                    $vars['conversation'][$key]->image = $image->getUserImageActive($value->getId());
                }
                $vars['chat_id_current'] = $args['chat_id'];
            }
        }
        if ($request->isXhr()){
            return $this->view->render($response, 'ajax/chat/conversation.twig', $vars);
        }
        return $this->view->render($response, 'chat.twig', $vars);
    }

    function getContacts($request, $response, $args)
    {
        if ($request->isXhr()){
            $user = $this->auth->user();
            $chat = new ChatUser();
            $vars['contacts'] = $chat->getAllContact($user->getId());
            if (!empty($args['chat_id'])){
                if ($chat->chatIdAndUserIdExist($args['chat_id'], $user->getId()))
                    $vars['chat_id_current'] = $args['chat_id'];
            }
            return $this->view->render($response, 'ajax/chat/contact.twig', $vars);
        }
    }

    public function postChatSend($request, $response)
    {
        if ($request->isXhr()){
            $validate = true;
            //$data = $this->sanitize->special_chars($request);
            $data = $request->getParams();
            if (empty($data['message'])){
                $validate = false;
                $ret['message'] = "Your message can't be empty. ";
            }
            elseif (mb_strlen($data['message']) > 3000)
                $data['message'] = mb_strcut($data['message'], 0, 3000);
            if (($data['chat_id'] = filter_var($data['chat_id'], FILTER_VALIDATE_INT, array("options" => array("min_range"=> 1)))) === false){
                if ($validate){
                    $validate = false;
                    $ret['message'] = 'Error type chat_id is not number.';
                }
                else
                    $ret['message'] .= 'Error type chat_id is not number.';
            }
            if ($validate){
                $user = $this->auth->user();
                $chat_user = new ChatUser();
                $chat_user = $chat_user->chatIdAndUserIdExist($data['chat_id'], $user->getId());
                if ($chat_user){
                    $chat_message = new ChatMessage();
                    $chat_message->AddMessageByChatId($data['chat_id'], $user->getId(), $data['message']);
                    $vars['conversation'] = $chat_message->getConversationByChatId($data['chat_id']);
                    $image  = new UserImage();
                    foreach ($vars['conversation'] as $key => $value) {
                        $vars['conversation'][$key]->image = $image->getUserImageActive($value->getId());
                    }
                    return $this->view->render($response, 'ajax/chat/conversation.twig', $vars);
                }
                else
                    $ret['message'] = 'the conversation has been closed.';
            }
            return $this->response->withJson($ret, 400);
        }
    }
}
