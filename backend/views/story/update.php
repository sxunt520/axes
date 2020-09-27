<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = '更新游戏: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '游戏列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="story-update">

    <?= $this->render('_form', [
        'model' => $model,

        'model2' => $model2,
        'p1' => $p1,
        'p2' => $p2,

        'model2_x' => $model2_x,
        'p1_x' => $p1_x,
        'p2_x' => $p2_x,

        'model2_v' => $model2_v,
        'p1_v' => $p1_v,
        'p2_v' => $p2_v,

        'id0' => $id0,
        'flag'=>$flag
    ]) ?>

</div>
