<?php
namespace App\Sanitize;

use Psr\Http\Message\ServerRequestInterface;
/**
 *
 */
class Sanitize{

    public function special_chars(ServerRequestInterface $request)
    {
        $diff = array("csrf_name" => "", "csrf_value" => "");
        $data = array_diff_key($request->getParams(), $diff);
        foreach ($data as $key => $value) {
            if (is_string($value) === true)
                $data[$key] = trim($value);
            else
                $data[$key] = $value;
        }
        $data = filter_var_array($data, FILTER_SANITIZE_STRING);
        return ($data);
    }
}
