<?php
/**
 * @author
 * 多图上传组件
 * @Date: 2017-07-14
 */
use yii\helpers\Url;

if(!empty(Yii::$app->params['images'])){
    $img_host=Yii::$app->params['images'];
}else{
    $img_host='http://app.wujieshenghuo.com';
}

return [
    /* 上传图片配置项 */
    'fieldName' => "fileData", /* 提交的图片表单名称 */
    'maxSize' => 2097152, /* 上传大小限制，单位B */
    'allowFiles'=> [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 上传图片格式显示 */
    'pathFormat'=> "/data/uploads/{yyyy}/{mm}/{dd}/{date}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    'uploadFilePath' => str_replace('backend', 'app', $_SERVER['DOCUMENT_ROOT']), /* 文件保存绝对路径   */
    'uploadType' => 'upload', //remote远程图片   base64 base64编码 upload 正常的上传方法,
    //'serverUrl' => Url::to('/admin/upload/upload_more'),
    'serverUrl' => '/goods/upload_more',
    'trueDelete' => 'true', //为TRUE是，点确定后， 将会把真实图片删除，为false时， 只会把父元素移除， 不会删除真实图片
    //'img_host'=>'http://app.wjsh.com',//图片的域名
    'img_host'=>$img_host,//图片的域名
];