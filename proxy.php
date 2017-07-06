<?php

$base_url = 'https://ktrade.kz';
$agent = $_SERVER['HTTP_USER_AGENT'] . ' GETTOUR';

$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_URL, $base_url . $_SERVER['REQUEST_URI']);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
}
else {
    curl_setopt($ch, CURLOPT_POST, 0);
}

$data = curl_exec($ch);

$data = explode('Accept-Encoding', $data);
$headers = $data[0];
$html = $data[1];

$matches = [];
preg_match('/(Location: \S+)/', $headers, $matches);
if(isset($matches[1])) {
    header($matches[1]);
}

$html = preg_replace('/"(\/[a-z])/', '"' . $base_url . '$1', $html);
echo $html;