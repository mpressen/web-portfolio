<?php

namespace App\Controllers\Profile;

use App\Models\User;
use App\Models\UserImage;
use App\Models\UserHasTag;
use App\Models\Tag;
use App\Controllers\Controller;

class ProfileController extends Controller
{
    public function getProfile($request,$response)
    {
        $usertags = array();
        $tags = array();
        $tag = new Tag();
        $user_loggued = $this->container->auth->user();
        $user = new User();
        $user = $user->getUserByIdWithTags($user_loggued->getId());
        foreach ($user->getTags() as $key => $value) {
            $usertags[] = $value->name;
        }
        foreach ($tag->getAllTags() as $key => $value) {
            $tags[] = $value->name;
        }
        $img = new UserImage();
        $gallery = $img->getImagesUrl($user->getId());
        return $this->view->render($response, 'profile/profile.twig', array(
            'user' => $user,
            'gallery' => $gallery,
            'usertags' => $usertags,
            'tags' => $tags
             ));
    }

    public function ajaxEmailUpdate($request, $response)
    {
        if ($request->isXhr())
        {
            $email = $request->getParam('val');
            $user = $this->auth->user();
            if ($this->validator->validationEmail($email))
            {
                if ($user->emailExists($email))
                {
                    $ret['status'] = 'error';
                    $ret['message'] = "This mail is already registered.";
                }
                else
                {
                    $user->update($user->getId(), 'email', $email);
                    $ret['status'] = 'success';
                    $ret['message'] = "Updated.";
                }
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $_SESSION["errors"]["email"];
                $ret['old'] = $user->getEmail();
                unset($_SESSION["errors"]["email"]);
            }
            return json_encode($ret);
        }
    }

    public function ajaxFirstnameUpdate($request,$response)
    {
        if ($request->isXhr())
        {
            $firstname = $request->getParam('val');
            $user = $this->auth->user();
            if ($this->validator->validationFirstname($firstname))
            {
                $user->update($user->getId(), 'firstname', $firstname);
                $ret['status'] = 'success';
                $ret['message'] = "Updated.";
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $_SESSION["errors"]["firstname"];
                $ret['old'] = $user->getFirstname();
                unset($_SESSION["errors"]["firstname"]);
            }
            return json_encode($ret);
        }
    }

    public function ajaxLastnameUpdate($request,$response)
    {
        if ($request->isXhr())
        {
            $lastname = $request->getParam('val');
            $user = $this->auth->user();
            if ($this->validator->validationLastname($lastname))
            {
                $user->update($user->getId(), 'lastname', $lastname);
                $ret['status'] = 'success';
                $ret['message'] = "Updated.";
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $_SESSION["errors"]["lastname"];
                $ret['old'] = $user->getLastname();
                unset($_SESSION["errors"]["lastname"]);
            }
            return json_encode($ret);
        }
    }

    public function ajaxAgeUpdate($request,$response)
    {
        if ($request->isXhr())
        {
            $age = $request->getParam('age');
            if ($age === "")
                $age = NULL;
            $user = $this->container->auth->user();
            if ($this->container->validator->validationAge($age))
            {
                $user->update($user->getId(), 'age', $age);
                $ret['status'] = 'success';
                $ret['message'] = "Updated.";
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $_SESSION["errors"]["age"];
                $ret['old'] = $user->getAge();
                unset($_SESSION["errors"]["age"]);
            }
            return json_encode($ret);
        }
    }

    public function ajaxGenderUpdate($request,$response)
    {
        if ($request->isXhr())
        {
            $gender = $request->getParam('gender');
            if ($gender === 'blank')
                $gender = NULL;
            $user = $this->container->auth->user();
            if ($this->container->validator->validationGender($gender))
            {
                $user->update($user->getId(), 'gender', $gender);
                $ret['status'] = 'success';
                $ret['message'] = "Updated.";
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $_SESSION["errors"]["gender"];
                $ret['old'] = $user->getGender();
                unset($_SESSION["errors"]["gender"]);
            }
            return json_encode($ret);
        }
    }

    public function ajaxAttractionUpdate($request,$response)
    {
        if ($request->isXhr())
        {
            $attraction = $request->getParam('attraction');
            $user = $this->container->auth->user();
            if ($this->container->validator->validationAttraction($attraction))
            {
                $user->update($user->getId(), 'attraction', $attraction);
                $ret['status'] = 'success';
                $ret['message'] = "Updated.";
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $_SESSION["errors"]["attraction"];
                $ret['old'] = $user->getAttraction();
                unset($_SESSION["errors"]["attraction"]);
            }
            return json_encode($ret);
        }
    }

    public function ajaxBioUpdate($request,$response)
    {
        if ($request->isXhr())
        {
            $bio = $request->getParam('bio');
            $user = $this->container->auth->user();
            if ($this->container->validator->validationBio($bio))
            {
                $user->update($user->getId(), 'bio', $bio);
                $ret['status'] = 'success';
                $ret['message'] = "Updated.";
            }
            else
            {
                $ret['status'] = 'error';
                $ret['message'] = $_SESSION["errors"]["bio"];
                $ret['old'] = $user->getBio();
                unset($_SESSION["errors"]["bio"]);
            }
            return json_encode($ret);
        }
    }

    public function ajaxTagNew($request, $response)
    {
        if ($request->isXhr()){
            $data = $this->sanitize->special_chars($request);
            if ($this->validator->validationTag($data['new'])){
                $usertag = new UserHasTag();
                $usertag->addTagByUserId($_SESSION['user'], $data['new']);
                $ret["status"] = 'success';
                $ret["message"] = 'ok';
            }
            else {
                $ret["status"] = 'error';
                $ret["message"] = $_SESSION['errors']['tag'];
                unset($_SESSION['errors']['tag']);
            }
            echo json_encode($ret);
        }
    }

    public function ajaxTagUpdate($request,$response)
    {
        if ($request->isXhr()){
            $data = $this->sanitize->special_chars($request);
            if ($this->validator->validationTag($data['new'])){
                $tag = new UserHasTag();
                $tag->deleteTagByName($data["old"], $_SESSION['user']);
                $tag->addTagByUserId($_SESSION['user'], $data['new']);
                $ret["status"] = 'success';
                $ret["message"] = 'ok';
            }
            else {
                $ret["status"] = 'error';
                $ret["message"] = $_SESSION['errors']['tag'];
                unset($_SESSION['errors']['tag']);
            }
            echo json_encode($data);
        }
    }

    public function ajaxTagDelete($request,$response)
    {
        if ($request->isXhr()){
            $data = $this->sanitize->special_chars($request);
            if ($this->validator->validationTag($data['tag'])){
                $tag = new UserHasTag();
                $tag->deleteTagByName($data["tag"], $_SESSION['user']);
                $ret["status"] = 'success';
                $ret["message"] = 'ok';
            }
            echo json_encode($data);
        }
    }
}