<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use autoheroes\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html style="overflow: hidden; height: 100%;" lang="zh-CN">
        <head>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8">
            <meta charset="utf-8">
            <title><?= Html::encode($this->title) ?></title>
            <link rel="shortcut icon" href="/images/favicon.ico" />
            <meta name="keywords" content="">
            <meta name="description" content="">
            <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
            
            <link rel="stylesheet" href="./css/reset.css">
            <link rel="stylesheet" href="./css/swiper.css">
            <link rel="stylesheet" href="./css/animate.css">
            <link rel="stylesheet" href="./css/share.css">
            <link rel="stylesheet" href="./css/style-xxxx.css">
            <link rel="stylesheet" href="./css/footer.css">
            <link rel="stylesheet" href="./css/my_swiper_xx.css">
            <script src="./js/1.7.2.min.js"></script>
            <script>
                //navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i) && (location.href = "index_mobile.html")
            </script>

            <?php $this->head() ?>
        </head>
        <body>
            <?php $this->beginBody() ?>

             <?= $content ?>

            <?php $this->endBody() ?>
        </body>
    </html>
<?php $this->endPage() ?>

