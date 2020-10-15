<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryAnnounce */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-announce-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'admin_id')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'story_id')->label('所属游戏')->dropDownList(\common\models\Story::getStoryRows(), ['prompt'=>'请选择游戏','style'=>'width:500px']) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => 30,'style'=>'width:600px']) ?>
    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'tagNames')->widget(\common\widgets\tag\Tag::className()) ?>

    <?php // $form->field($model, 'views')->textInput(['maxlength' => true]) ?>
    <?php // $form->field($model, 'likes')->textInput(['maxlength' => true]) ?>
    <?php // $form->field($model, 'share_num')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'pic_cover')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->label('官方旅人号id')->textInput(['maxlength' => 11,'style'=>'width:100px','value'=>!empty($model->user_id)?$model->user_id:12351]) ?>

    <?= $form->field($model, 'is_show')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>
    <?= $form->field($model, 'order_by')->label('排序值:格式数字(数字越大排名越靠前)')->textInput(['maxlength' => 11,'style'=>'width:100px','value'=>!empty($model->order_by)?$model->order_by:100]) ?>

    <?php // $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
