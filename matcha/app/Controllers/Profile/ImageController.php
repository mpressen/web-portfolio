<?php
namespace App\Controllers\Profile;

use App\Models\User;
use App\Models\UserImage;
use App\Controllers\Controller;

/**
 *
 */
class ImageController extends Controller
{
    public function postImgAdd($request,$response)
    {
        if ($request->isXhr() && isset($_FILES['myFile']) && ($pic_num = $request->getParam("pic_num")) && $this->validator->validationPosition($pic_num))
        {
            $picture = $_FILES['myFile']['tmp_name'];
            $name_pic = $_FILES['myFile']['name'];
            $size_pic = $_FILES['myFile']['size'];
            $imageFileType = pathinfo($name_pic, PATHINFO_EXTENSION);
            $status = $this->validator->validationPicture($picture, $name_pic, $size_pic, $imageFileType);
            if ($status == 1)
            {
             $user = $this->auth->user();
             $user_id = $user->getId();

             self::createFolder("./assets/uploads/");
             $target_dir = self::createFolder("./assets/uploads/" . $user_id . "/");

             $picRandName = uniqid();
             $target_file = $target_dir . $picRandName . "." . $imageFileType;

             if (move_uploaded_file($picture, $target_file))
             {
                //self::resize($target_file);

                $db_image = new UserImage();
                $ret['isactive'] = $db_image->addImage($target_file, $user_id, $pic_num);
                $ret['picture_url'] = $target_file;
                return json_encode($ret);
            }
            $ret['message'] = "Error with this picture. Try upload another";
            return json_encode($ret);
        }
        $ret['message'] = $status;
        return json_encode($ret);
    }
    $ret['message'] = "Invalid process. Pictures only";
    return json_encode($ret);
}

public function postImgDel($request, $response)
{
    if ($request->isXhr() && ($pic_num = $request->getParam("pic_num")) && $this->validator->validationPosition($pic_num))
    {
        $db_image = new UserImage();
        $user = $this->auth->user();
        $user_id = $user->getId();
        return ($db_image->deleteImage($pic_num, $user_id));
    }   
}

public function postImgProfileChange($request, $response)
{
    if ($request->isXhr() && ($pic_num = $request->getParam("pic_num")) && $this->validator->validationPosition($pic_num))
    {
        $db_image = new UserImage();
        $user = $this->auth->user();
        $user_id = $user->getId();
        return ($db_image->setImageProfile($pic_num, $user_id));
    }   
}

public function postImgProfileGet($request, $response)
{
 if ($request->isXhr())
 {
    $db_image = new UserImage();
    $user = $this->auth->user();
    $user_id = $user->getId();
    return ($db_image->getImageProfilePosition($user_id));
}   
}

private function createFolder($folder)
{
    if (file_exists($folder) && is_dir($folder))
        return ($folder);
    mkdir($folder);
    return($folder);
}

private function resize($file)
{
    $x = 640;
    $y = 480;
    $size = getimagesize($file);
    if ($size)
    {
        if ($size['mime'] =='image/jpeg')
        {
            $img_big = imagecreatefromjpeg($file);
            $img_new = imagecreate($x, $y);
            $img_mini = imagecreatetruecolor($x, $y);
            imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
            imagejpeg($img_mini, $file);
        }
        else if ($size['mime']=='image/png' )
        {
            $img_big = imagecreatefrompng($file);
            $img_new = imagecreate($x, $y);
            $img_mini = imagecreatetruecolor($x, $y);
            imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
            imagepng($img_mini, $file);
        }
        else if ($size['mime']=='image/gif' )
        {
            $img_big = imagecreatefromgif($file);
            $img_new = imagecreate($x, $y);
            $img_mini = imagecreatetruecolor($x, $y);
            imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);
            imagegif($img_mini, $file);
        }
    }
}

}