<?php
namespace App\Location;

class Location
{
    private $latitude;
    private $longitude;
    private $postal_code;
    private $locality;
    private $country;
    private $full_address;
    private $status;
    private $error_message;
    private $format = "json";
    static private $key = "AIzaSyA0lPjg9RBmnbxqH2guD7j0aNoCUKFKQpc";
    static private $url = "https://maps.googleapis.com/maps/api/geocode/";

    function __construct(array $kargs = null)
    {
        if (isset($kargs) && !empty($kargs)){
            foreach($this as $key => $value) {
                if (array_key_exists($key, $kargs)){
                    $this->$key = $kargs[$key];
                }
            }
        }
        return $this;
    }

    public function geolocationByIp()
    {
        try
        {
            $url = "http://ip-api.com/json";
            $fd = curl_init();
            curl_setopt($fd, CURLOPT_URL, $url);
            curl_setopt($fd, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($fd);
            $rep = json_decode($data);
            if ($rep->status === "success" && $this->reverse_geocoding($rep->lat, $rep->lon)!== false)
                    return $this;
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    /**
     * get information of geolocation googleapis of $full_address
     *
     * @param  string  $full_address
     * @param  string  $param (optional) option request
     * @return Object  Location
     */
    public function geocoding($full_address, $param = null)
    {
        if (!empty($full_address)){
            $url = $this->getUrl().$this->getFormat()."?address=".urlencode($full_address)."&key=".$this->getKey().$param;
        }
        else {
            $url = $this->getUrl().$this->getFormat().$param."&key=".$this->getKey();
        }
        $fd = curl_init();
        curl_setopt($fd, CURLOPT_URL, $url);
        curl_setopt($fd, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($fd);
        $rep = json_decode($data, true);
        $this->setStatus($rep["status"]);
        if (!empty($rep))
        {
            if ($rep["status"] === "OK")
            {
                $results = $rep["results"][0];
                $this->setFullAddress($results["formatted_address"]);
                $components = $results["address_components"];
                foreach ($components as $component)
                {
                    if (array_key_exists("types", $component))
                    {
                        foreach ($component["types"] as $value)
                        {
                            if ($value === "postal_code")
                                $this->setPostalCode($component["long_name"]);
                            else if ($value === "locality")
                                $this->setLocality($component["long_name"]);
                            else if ($value === "country")
                                $this->setCountry($component["long_name"]);
                        }
                    }
                }
                if (!empty($results["geometry"]["location"])){
                        $this->setLatitude($results["geometry"]["location"]["lat"]);
                        $this->setLongitude($results["geometry"]["location"]["lng"]);
                }
            }
            else
            {
                if (array_key_exists("error_message", $rep))
                    $this->setErrorMessage($rep["error_message"]);
                return (false);
            }
            return $this;
        }
        return false;
    }

    /**
     * get information of geolocation googleapis by $latitude and $longitude
     *
     * @param  string  $latitude
     * @param  string  $longitude
     * @param  string  $param (optional) option request
     * @return Object  Location if success or false
     */
    function reverse_geocoding($latitude, $longitude, $param = null)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        try {
            $url = $this->getUrl().$this->getFormat()."?latlng=".$this->getLatitude().",".$this->getLongitude()."&key=".$this->getKey()."&language=fr&result_type=postal_code".$param;
            $fd = curl_init();
            curl_setopt($fd, CURLOPT_URL, $url);
            curl_setopt($fd, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($fd);
            $rep = json_decode($data, true);
        } catch (Exception $e) {
            return false;
        }
        $this->setStatus($rep["status"]);
        if ($rep["status"] === "OK")
        {
            $results = $rep["results"][0];
            $this->setFullAddress($results["formatted_address"]);
            $components = $results["address_components"];
            foreach ($components as $component)
            {
                if (array_key_exists("types", $component))
                {
                    foreach ($component["types"] as $value)
                    {
                        if ($value === "postal_code")
                            $this->setPostalCode($component["long_name"]);
                        else if ($value === "locality")
                            $this->setLocality($component["long_name"]);
                        else if ($value === "country")
                            $this->setCountry($component["long_name"]);
                    }
                }
            }
            if (!empty($results["geometry"]["location"])){
                    $this->setLatitude($results["geometry"]["location"]["lat"]);
                    $this->setLongitude($results["geometry"]["location"]["lng"]);
            }
        }
        else
        {
            if ($rep && array_key_exists("error_message", $rep))
                $this->setErrorMessage($rep["error_message"]);
            return (false);
        }
        return $this;
    }

    /**
     * convert instance of location to array
     *
     * @return Array
     */
    public function toArray()
    {
        $rep = array();
        foreach($this as $key => $value) {
            $rep[$key] = $value;
        }
        return $rep;
    }

    /**
     * Get the value of Latitude
     *
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the value of Latitude
     *
     * @param mixed latitude
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        $latitude = filter_var($latitude, FILTER_VALIDATE_FLOAT);
        if ($latitude !== false)
            $this->latitude = $latitude;
        return $this;
    }

    /**
     * Get the value of Longitude
     *
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the value of Longitude
     *
     * @param mixed longitude
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        $longitude = filter_var($longitude, FILTER_VALIDATE_FLOAT);
        if ($longitude !== false)
            $this->longitude = $longitude;
        return $this;
    }

    /**
     * Get the value of Country
     *
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of Country
     *
     * @param mixed country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of Full Address
     *
     * @return mixed
     */
    public function getFullAddress()
    {
        return $this->full_address;
    }

    /**
     * Set the value of Full Address
     *
     * @param mixed full_address
     *
     * @return self
     */
    public function setFullAddress($full_address)
    {
        $this->full_address = $full_address;

        return $this;
    }

    /**
     * Get the value of Key
     *
     * @return mixed
     */
    public function getKey()
    {
        return self::$key;
    }


    /**
     * Get the value of Url
     *
     * @return mixed
     */
    public function getUrl()
    {
        return self::$url;
    }

    /**
     * Get the value of Status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of Status
     *
     * @param mixed status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    /**
     * Get the value of Format
     *
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the value of Format
     *
     * @param mixed format
     *
     * @return self
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Set the value of Url
     *
     * @param mixed url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }


    /**
     * Get the value of Postal Code
     *
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * Set the value of Postal Code
     *
     * @param mixed postal_code
     *
     * @return self
     */
    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * Get the value of Locality
     *
     * @return mixed
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set the value of Locality
     *
     * @param mixed locality
     *
     * @return self
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Set the value of Key
     *
     * @param mixed key
     *
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }


    /**
     * Get the value of Error Message
     *
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Set the value of Error Message
     *
     * @param mixed error_message
     *
     * @return self
     */
    public function setErrorMessage($error_message)
    {
        $this->error_message = $error_message;

        return $this;
    }

}