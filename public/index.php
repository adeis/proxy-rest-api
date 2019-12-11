<?php

require '../vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$env = new Dotenv(true);
$env->load(__DIR__.'/../.env');
function env($key, $def = '')
{
    return isset($_ENV[$key]) ? $_ENV[$key] : $def;
}
$destUrl = env('BASE_API');

$h = [];
$arr = [];
$skip = ['Host'];
$respHeader = '';
foreach (getallheaders() as $name => $value) {
    $arr[$name] = $value;
    if ($name == 'Access-Control-Request-Headers') {
        $respHeader = $value;
    }
    if (!in_array($name, $skip)) {
        $h[] = "$name: $value";
    }
}

$curl = curl_init();
$endpoint = $destUrl.$_SERVER['REQUEST_URI'];

$method = $_SERVER['REQUEST_METHOD'];
$opt = [
  CURLOPT_URL => $endpoint,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => $method,
  CURLOPT_HTTPHEADER => $h,
];
curl_setopt_array($curl, $opt);
if ($method == 'POST') {
    $dataBody = file_get_contents('php://input');
    $dataBody = empty($dataBody) ? $_POST : $dataBody;
    if (empty($dataBody)) {
        curl_setopt($curl, CURLOPT_POST, true);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $dataBody);
}
if ($method == 'PUT') {
    $dataBody = file_get_contents('php://input');
    $dataBody = empty($dataBody) ? $_POST : $dataBody;
    curl_setopt($curl, CURLOPT_POSTFIELDS, $dataBody);
}
$response = curl_exec($curl);
$info = curl_getinfo($curl);
$httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

$err = curl_error($curl);

curl_close($curl);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Method: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header("Access-Control-Allow-Headers: $respHeader");

if ($err) {
    header('Content-Type: '.$info['content_type']);
    http_response_code($httpcode);

    echo $err;
} else {
    header('Content-Type: '.$info['content_type']);
    http_response_code($httpcode);

    echo $response;
}
