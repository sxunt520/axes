<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SensitiveWords */

$this->title = '创建敏感词';
$this->params['breadcrumbs'][] = ['label' => '评论敏感词列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensitive-words-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
