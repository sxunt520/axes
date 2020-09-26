<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html style="overflow: hidden; height: 100%;" lang="zh-CN">
        <head>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8">
            <meta charset="utf-8">
            <title><?= Html::encode($this->title) ?></title>
            <meta name="keywords" content="">
            <meta name="description" content="">
            <link rel="stylesheet" href="/css/fullPage.css">
            <link rel="stylesheet" href="/css/my_index.css">
            <style>
                .section { text-align: center; font: 50px "Microsoft Yahei"; color: #fff;}
            </style>
            <script src="/js/jquery_002.js"></script>
            <script src="/js/fullPage.js"></script>
            <?php $this->head() ?>
        </head>
        <body>
            <?php $this->beginBody() ?>

             <?= $content ?>

            <?php $this->endBody() ?>
        </body>
    </html>
<?php $this->endPage() ?>

