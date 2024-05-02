<?php

require 'vendor/autoload.php';

use Otp\Otp;
use ParagonIE\ConstantTime\Encoding;

const EMAIL = 'totp@authenticationtest.com';
const PASSWORD = 'pa$$w0rd';
const SECRET = 'I65VU7K5ZQL7WB4E';

// Get the OTP
$otp = new Otp;

$key = $otp->totp(Encoding::base32DecodeUpper(SECRET));

// Log in
$query = http_build_query([
    'email' => EMAIL,
    'password' => PASSWORD,
    'totpmfa' => $key
]);

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://authenticationtest.com/login/?mode=totpChallenge',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $query,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true
]);

$html = curl_exec($ch);

if (curl_errno($ch)) {
    echo curl_error($ch);
}

curl_close($ch);

// Parse the response
$document = new DOMDocument();

if (! $document->loadHTML($html)) {
    echo "Failed to load HTML\n";
}

$xpath = new DOMXPath($document);

$nodeList = $xpath->query("//div[@class='alert alert-success']");

$node = $nodeList->item(0);

var_dump($node->nodeValue);