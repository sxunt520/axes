<?php

/* @var $this yii\web\View */

$this->title = '控制面板';
?>
<div class="site-index">

    <div class="jumbotron">
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?=\common\models\StoryRecommend::find()->count()?><sup style="font-size: 20px">个</sup></h3>
                        <p>当前故事数</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-document"></i>
                    </div>
                    <a href="/admin/story-recommend/index" class="small-box-footer">更多信息 <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?=\common\models\StoryComment::find()->count()?><sup style="font-size: 20px">条</sup></h3>
                        <p>当前评论</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="#" class="small-box-footer">更多信息 <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?=\common\models\Member::find()->count()?><sup style="font-size: 20px">人</sup></h3>
                        <p>当前注册用户</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="#" class="small-box-footer">更多信息 <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
        </div><!-- /.row -->
    </div>

</div>
