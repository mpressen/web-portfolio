<?php

namespace App\Validation;


use App\Models\User;
/**
*
*/
class Validator
{

	/**
	* Execute si existe les méthodes de validation par rapport au clés présent dans le tableau
	*
	* exemple:
	* $data = {'username' => 'sdjeffal', 'email' => 'sdjeffal@student.42.fr', 'unknown' => 'shit'}
	* Validator::validation($data) //les methodes validationUsername validationEmail seront appelées pour les champs username et email. La methode validationUnknown n'existant, le champs unknown ne sera pas vérifié.
	*/
	public static function validation($data)
	{
		$rep  = true;
		foreach ($data as $key => $value) {
			$method = "validation".ucfirst($key);
			if (method_exists(__class__, $method))
			{
				$validation = new Validator();
				if ($validation->$method($data[$key]) === false && $rep === true)
					$rep = false;
			}
		}
		return ($rep);
	}

	public function validationUsername($name)
	{
		if (preg_match('/^[\p{L}|0-9]{1,45}$/u', $name))
			return true;
		$_SESSION["errors"]["username"] = "Your username must have between 1 and 64 characters alphanumerics.";
		return false;
	}

	public function validationFirstname($name)
	{
		if (preg_match('/^[\p{L}]{1,45}$/u', $name))
			return true;
		$_SESSION["errors"]["firstname"] = "Your firstname must have between 1 and 64 characters alphabetics.";
		return false;
	}

	public function validationLastname($name)
	{
		if (preg_match('/^[\p{L}]{1,45}$/u', $name))
			return true;
		$_SESSION["errors"]["lastname"] = "Your lastname must have between 1 and 64 characters alphabetics.";
		return false;
	}

	public function validationTag($tag)
	{
		if (preg_match('/^[\p{L}|\p{N}]+([\-|_][\p{L}|\p{N}]+)?$/u', $tag) && strlen($tag) <= 255)
			return true;
		$_SESSION["errors"]["tag"] = "A tag must have between 1 and 255 characters alphabetics and it can contain a - or a _ .";
		return false;
	}

	public function validationEmail($email)
	{
		if (($email = filter_var($email, FILTER_VALIDATE_EMAIL)) !== false)
			return true;
		$_SESSION["errors"]["email"] = "Invalid email address.";
		return false;
	}

	public function validationPassword($passwd)
	{
		if (preg_match("/^[a-z0-9]{6,24}$/i", $passwd) && preg_match("/[a-z]+[A-Z]+[0-9]+|[a-z]+[0-9]+[A-Z]+|[A-Z]+[a-z]+[0-9]+|[A-Z]+[0-9]+[a-z]+|[0-9]+[a-z]+[A-Z]+|[0-9]+[A-Z]+[a-z]+/", $passwd))
			$boolMatch = true;
		else
		{
			$_SESSION["errors"]["password"] = "Your password must contain between 6 and 24 alphanumeric characters with at least one lower case, one uppercase letter and one digit.";
			$boolMatch = false;
		}
		return $boolMatch;
	}

	public function validationAge($age)
	{
		if ((is_numeric($age) && $age > 17 && $age < 151) || $age === NULL)
			return true;
		$_SESSION["errors"]["age"] = "You must enter a valid age.";
		return false;
	}

	public function validationGender($gender)
	{
		if ($gender === 'male' || $gender === 'female' || $gender === NULL)
			return true;
		$_SESSION["errors"]["gender"] = "You must enter a valid gender.";
		return false;
	}

	public function validationAttraction($attraction)
	{
		if ($attraction === 'heterosexual' || $attraction === 'bisexual' || $attraction === 'homosexual')
			return true;
		$_SESSION["errors"]["attraction"] = "You must enter a valid sexual attraction.";
		return false;
	}

	public function validationBio($bio)
	{
		if (strlen($bio) < 1000)
			return true;
		$_SESSION["errors"]["bio"] = "1000 characters maximum.";
		return false;
	}

	public function validationPopularity($popularity)
	{
		if (is_numeric($popularity) && $popularity >= 0 && $popularity <= 1000)
			return true;
		return false;
	}

	public function validationLatitude($latitude)
	{
		if ((float)$latitude >= -90.0 && (float)$latitude <= 90.0)
			return true;
		return false;
	}

	public function validationLongitude($longitude)
	{
		if ((float)$longitude >= -180.0 && (float)$longitude <= 180.0)
			return true;
		return false;
	}

	public function validationPicture($picture, $name_pic, $size_pic, $imageFileType)
	{
		$imageFileType = strtolower($imageFileType);
		if (preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_pic))
			return "Invalid file name.";
		else if(getimagesize($picture) === false)
			return "Invalid format. Pictures only";
		else if ($size_pic > 1000000)
			return "Picture's size can't exceed 1 Mo";
		else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif")
			return "You can only import pictures with .png, .jpeg, .jpg, .gif extensions";
		return 1;
	}

	public function validationPosition($position)
	{
		if ((int)$position < 6 && (int)$position > 0)
			return true;
		return false; 
	}

	public function SecureProperId($user_id_entry, $user_id)
	{
		if ((int)$user_id_entry == $user_id)
			return true;
		return false; 
	}
}