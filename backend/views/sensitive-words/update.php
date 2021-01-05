<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SensitiveWords */

$this->title = '更新创建敏感词: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '评论敏感词列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="sensitive-words-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
