<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SensitiveKeywords */

$this->title = '更新敏感词: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '敏感词列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="sensitive-keywords-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
