<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-video-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'story_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'video_url')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'video_cover')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'video_cover')->widget('common\widgets\file_upload\FileUpload',[
        'config'=>[
            //图片上传的一些配置，不写调用默认配置
            'domain_url' => Yii::getAlias('@static'),////图片域名
            'serverUrl' => yii\helpers\Url::to(['upload_one','action'=>'uploadimage','is_thumb'=>true,'adv_width'=>720,'adv_height'=>420]),  //上传服务器地址 is_thumb就否返回生成缩略图
        ]
    ])->label('宣传视频封面图(720*420px|16:9 大小不超过500k)') ?>

    <?php // $form->field($model, 'video_type')->textInput() ?>

    <?php // $form->field($model, 'is_show')->textInput() ?>

    <?php // $form->field($model, 'views')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'likes')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'share_num')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
