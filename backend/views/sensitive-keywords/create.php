<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SensitiveKeywords */

$this->title = '新增敏感词';
$this->params['breadcrumbs'][] = ['label' => 'Sensitive Keywords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensitive-keywords-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
