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
            <link rel="shortcut icon" href="/images/favicon.ico" />
            <meta name="keywords" content="阅读平台、免费阅读平台、故事阅读平台 、手机阅读平台、网页阅读、有声小说、漫画、旅人计划、游戏下载、游戏平台、广播剧">
            <meta name="description" content="旅人计划（233i.com)是国内超S级阅读平台，拥有独家内容：泡沫冬景、孟德大小姐与自爆少年、云端之约、长安夜明等；我们秉持每个故事都是一段旅程的理念为用户提供高质量私享阅读体验，营造良好社区氛围，期待每位用户都能在此遇见万里挑一的灵魂。">
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

