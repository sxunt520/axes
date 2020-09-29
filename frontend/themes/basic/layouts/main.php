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
            <meta name="keywords" content="旅人 旅人计划 泡沫冬景 云端之约 圣歌德嘉的晚钟 青箱 长安夜明 孟德大小姐与自爆少年">
            <meta name="description" content="旅人计划专注于发行独特而有内核的游戏，我们相信，每一款游戏都是一段珍贵的旅途。我们想带你看中世纪教堂顶的信仰之跃，带你看未来世界赛博朋克的魅力，带你看看奇幻世界不可思议的冒险。">
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

