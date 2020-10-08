<?php

require dirname(__FILE__) . '/../vendor/autoload.php';

$secretId = "COS_SECRETID"; //"云 API 密钥 SecretId";
$secretKey = "COS_SECRETKEY"; //"云 API 密钥 SecretKey";
$region = "ap-beijing"; //设置一个默认的存储桶地域
$cosClient = new Qcloud\Cos\Client(
    array(
        'region' => $region,
        'schema' => 'http', //协议头部，默认为http
        'credentials'=> array(
            'secretId'  => $secretId ,
            'secretKey' => $secretKey)));
//$local_path = "/Applications/XAMPP/web/demo/jjjj.png";
$local_path = "/Users/sxunt/Downloads/NEXT/picture/jjjj.png";
try {
    $result = $cosClient->upload(
        $bucket = 'sxunt-1303818459', //格式：BucketName-APPID
        $key = date("Ymd").'/'.uniqid().'.png',
        $body = fopen($local_path, 'rb')
        /*
        $options = array(
            'ACL' => 'string',
            'CacheControl' => 'string',
            'ContentDisposition' => 'string',
            'ContentEncoding' => 'string',
            'ContentLanguage' => 'string',
            'ContentLength' => integer,
            'ContentType' => 'string',
            'Expires' => 'string',
            'GrantFullControl' => 'string',
            'GrantRead' => 'string',
            'GrantWrite' => 'string',
            'Metadata' => array(
                'string' => 'string',
            ),
            'ContentMD5' => 'string',
            'ServerSideEncryption' => 'string',
            'StorageClass' => 'string'
        )
        */
    );
    // 请求成功
    print_r($result);
} catch (\Exception $e) {
    // 请求失败
    echo($e);
}

