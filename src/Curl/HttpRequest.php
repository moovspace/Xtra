<?php
namespace Xtra\Curl;
use \Exception;

class HttpRequest
{
    static function HttpPost ($url, $data, $json = false, $selfsigned = false, $token = '', $cookie = '', $user = '', $pass = '', $timeout = 60){

        $data_url = http_build_query ($data);
        $data_len = strlen ($data_url);

        if($json == true){
            // text/html,
            // content-type application/x-www-form-urlencoded
            $content = "Content-type: application/json; charset=utf-8";
            $data_url = json_encode($data);
            $data_len = strlen ($data_url);
        }

        if(!empty($user) && !empty($pass)){
            $auth = "Authorization: Basic ".base64_encode("$user:$pass")."\r\n";
        }

        if(!empty($token)){
            $token = "Authorization: Bearer ".$token;
        }

        if(!empty($cookie)){
            $cookie = "Cookie: ".$cookie."\r\n";
        }

        if($selfsigned == true){
            $ssl = array(
                "verify_peer" => false,
                "allow_self_signed" => true,
            );
        }else{
            $ssl = array(
                "verify_peer" => true,
                "allow_self_signed" => false,
            );
        }

        return array (
            'content' =>
                file_get_contents (
                    $url,
                    false,
                    stream_context_create (
                        array (
                            'ssl' => $ssl,
                            'http' =>
                            array (
                                'timeout' => (int) $timeout,
                                'method' => 'POST',
                                'protocol_version' => '1.1',
                                'header' =>
                                        $content .
                                        "Access-Control-Allow-Origin: *".
                                        "X-Frame-Options: sameorigin".
                                        "Connection: close\r\n"
                                        ."Content-Length: $data_len\r\n"
                                        .$auth.$cookie.$token,
                                'content' => $data_url
                            )
                        )
                    )
                )
            ,'headers' => $http_response_header
        );
    }
}

/*
$url = 'http://test.xx/route/user-route.route';
$url = 'http://test.xx/api.php';

$res = HttpRequest::HttpPost($url, array('name' => 'Jombo', 'id' => 247), true);
print_r($res);
*/
?>
