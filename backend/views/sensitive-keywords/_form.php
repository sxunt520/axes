<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SensitiveKeywords */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sensitive-keywords-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'word')->textarea(['rows' => 6])->label('敏感词组&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #DD4B39;">用 | 分割各个敏感词，特殊符号加反斜杠\&nbsp;&nbsp;如:台独\*|中共十七大\*  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;相关特殊符号: . \ + * ? [ ^ ] $ ( ) { } = ! < > | : -</span>') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
