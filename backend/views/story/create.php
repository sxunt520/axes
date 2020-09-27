<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = '新增游戏';
$this->params['breadcrumbs'][] = ['label' => '游戏列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-create">

    <?= $this->render('_form', [
        'model' => $model,

        'model2' => '',
        'p1' => '',
        'p2' => '',

        'model2_x' => '',
        'p1_x' => '',
        'p2_x' => '',

        'model2_v' => '',
        'p1_v' => '',
        'p2_v' => '',

        'id0' => '',
        'flag'=>0
    ]) ?>

</div>
