<?php

function generateNDigitRandomNumber($length){
    return mt_rand(pow(10,($length-1)),pow(10,$length)-1);
}

$url = 'http://localhost:8000/api/discountWalletCharger';

$file = fopen('output.txt', 'a');

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'mobile' => generateNDigitRandomNumber(11),
    'discount_code' => 'worldcup'
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);


fwrite($file, $result);


fclose($file);
