<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Member */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="member-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'real_name')->textInput(['maxlength' => 6,'style'=>'width:300px']) ?>
    <?= $form->field($model, 'real_idCard')->textInput(['maxlength' => 18,'style'=>'width:300px']) ?>

    <?php // $form->field($model, 'real_name_status')->textInput() ?>
    <?= $form->field($model, 'real_name_status')->dropDownList(['0'=>'未认证','1'=>'已认证','认证没通过'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : '操作', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
