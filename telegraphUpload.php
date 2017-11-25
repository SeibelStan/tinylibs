<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function telegraphUpload($data) {
    $domain = 'http://telegra.ph';

    $dataIsArray = gettype($data) == 'array';
    if(!$dataIsArray) {
        $data = [$data];
    }

    $result = [];
    foreach($data as $file) {
        if(!file_exists($file)) {
            continue;
        }
        $ext = mb_strtolower(pathinfo($file)['extension']);
        $ext = preg_replace('/jpeg/', 'jpg', $ext);
        $extValid = in_array($ext, ['png', 'jpg', 'gif']);
        $sizeValid = filesize($file) < 5 * 1024 * 1024;
        if(!$extValid || !$sizeValid) {
            continue;
        }
        
        $curlFile = curl_file_create($file);
        $curlFile->mime = 'image/' . $ext; // если надо видео и другое, сделай if
        $curlFile->postname = 'blob';
        $postData = [
            'file' => $curlFile
        ];

        $ch = curl_init($domain . '/upload');
        curl_setopt_array($ch, [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10
        ]);
        $request = curl_exec($ch);
        if($request) {
            $response = json_decode($request);
            $result[] = $domain . $response[0]->src;
        }
        curl_close($ch);        
    }

    if(!$result) {
        return false;
    }
    return $dataIsArray ? $result : $result[0];
}

print_r(telegraphUpload('icon24.png'));
print_r(telegraphUpload([
    'icon24.png',
    'icon24 (copy 1).png'
]));