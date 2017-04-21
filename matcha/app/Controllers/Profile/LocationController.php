<?php
namespace App\Controllers\Profile;

use App\Models\User;
use App\Controllers\Controller;

class LocationController extends Controller
{
    public function getLocateMe($request, $response)
	{
        $data = $this->sanitize->special_chars($request);
        $location = $this->location;
        if ($request->isXhr()){
    		if($location->reverse_geocoding($data["latitude"], $data["longitude"]) !== false || $location->geolocationByIp() !== false)
    		{
    			echo json_encode(
                    array(
                        'status' => 'OK',
                        'postal_code' => $location->getPostalCode(),
                        'locality' => $location->getLocality(),
                        'country' => $location->getCountry()
                        )
                );
    		}
            else {
                echo json_encode(
                    array(
                        'status' => 'ERROR',
                        'message' => 'the geolocation don\'t work.',
                        )
                );
            }
        }
	}

    public function postLocateUpdate($request, $response)
    {
        if ($request->isXhr()){
            $data = $this->sanitize->special_chars($request);
            $full_address = sprintf('%s %s, %s', $data['postal_code'], $data['locality'], $data['country']);
            $param = sprintf('&components=postal_code:%s|locality:%s|country:%s', urlencode($data['postal_code']), urlencode($data['locality']), urlencode($data['country']));
            $this->location->geocoding($full_address, $param);
            $user = $this->auth->user();
            if ($this->location->getStatus() === 'OK'
                && strcasecmp($this->location->getPostalCode(), $data['postal_code']) === 0
                && strcasecmp($this->location->getLocality(), $data['locality']) === 0
                && strcasecmp($this->location->getCountry(), $data['country']) === 0){
                    $user->updateLocation($user->getId(), $this->location);
                    echo json_encode(
                        array(
                            'status' => $this->location->getStatus(),
                            'message'=> 'Place found',
                            'postal_code' => $this->location->getPostalCode(),
                            'locality' => $this->location->getLocality(),
                            'country' => $this->location->getCountry()
                            )
                    );
            }
            else {

                echo json_encode(
                    array(
                        'status' => 'ERROR',
                        'message' => 'this place not exist',
                        'postal_code' => $user->getPostalCode(),
                        'locality' => $user->getLocality(),
                        'country' => $user->getCountry()
                        )
                );
            }
        }
    }
}
