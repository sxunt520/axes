<?php

/* @var $this yii\web\View */

$this->title = '超S级互动故事阅读APP';
?>
<header class="page-header">
    <div class="qrcode-box">
        <div id="J_QrcodeText" class="qrcode-text">点我扫二维码</div>
        <div id="J_QrcodeMain" class="qrcode-main" style="background:url(/m_static/image/qr.jpg) no-repeat center/cover;"></div>
    </div>
    <h1 class="logo"><a href="/">旅人计划</a></h1>
</header>

<div class="main wrap">
    <h1 class="app-logo">旅人计划</h1>
    <div class="carousel">
        <div class="img-list">
            <a class="J_ImgItem img-item img-item-xx1"></a>
            <a class="J_ImgItem img-item img-item-xx2"></a>
            <a class="J_ImgItem img-item img-item-xx3"></a>
            <a class="J_ImgItem img-item img-item-xx4"></a>
        </div>
        <ul class="J_SwitchList switch-list"></ul>
    </div>
    <div class="download-area">
        <div class="download-content">
            <div id="J_IntroBox" class="intro-box"></div>
            <div class="button-box">
                <p class="download-ios"><a class="btn-download" href="javascript:void(0);"><i class="icon-apple"></i>iPhone</a></p>
                <p class="download-android"><a class="btn-download" href="javascript:void(0);"><i class="icon-android"></i>Android</a></p>
                <p class="download-combo"><a class="btn-download" href="javascript:void(0);" onClick="return confirm('即将上线，敬请期待！');">立即下载</a></p>
            </div>
        </div>
    </div>
</div>
<div class="icp">
    <p><a target="_blank" href="http://www.beian.miit.gov.cn/"> © 2020 www.233i.com All rights reserved 蜀ICP备18015972号 川网文（2018）9245-339号</a></p>
</div>
<script src="/m_static/js/jquery.js"></script>
<script src="/m_static/js/index.js"></script>