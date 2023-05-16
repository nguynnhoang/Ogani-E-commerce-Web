<?php
    return [
        'vnp_tmncode' => env('VNP_TMNCODE'), //Mã định danh merchant kết nối (Terminal Id)
        'vnp_hashsecret' => env('VNP_HASHSECRET'), //Secret key
        'vnp_url' => env('VNP_URL'),
        'vnp_returnurl' => env('VNP_RETURNURL'),
        'vnp_apiurl' => env('VNP_APIURL '),
        'apiurl' => env('VNP_APIURL_TRANSACTION '),
    ];
?>