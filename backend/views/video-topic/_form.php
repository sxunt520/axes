<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideoTopic */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-video-topic-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'story_id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'story_id')->label('所属游戏')->dropDownList(\common\models\Story::getStoryRows(), ['prompt'=>'请选择游戏','style'=>'width:500px']) ?>

    <?= $form->field($model, 'topic_title')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?php // $form->field($model, 'topic_cover')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'topic_cover')->widget('common\widgets\file_upload\FileUpload',[
        'config'=>[
            //图片上传的一些配置，不写调用默认配置
            'domain_url' => Yii::getAlias('@static'),////图片域名
            'serverUrl' => yii\helpers\Url::to(['upload_one','action'=>'uploadimage','is_thumb'=>false,'adv_width'=>720,'adv_height'=>720]),  //上传服务器地址 is_thumb就否返回生成缩略图
        ]
    ])->label('主题封面(上传尺寸>720*720px,大小<1M ,jpg、png、gif、webp)') ?>

    <?php // $form->field($model, 'is_show')->textInput() ?>
    <?= $form->field($model, 'is_show')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?php // $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
