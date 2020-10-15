<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\StoryAnnounce */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '游戏公告', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-announce-view">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'admin_id',
            'user_id',
            'title',
            'content',
            'is_show',
            'views',
            'likes',
            'share_num',
            //'pic_cover',
            'order_by',
            'created_at',
            'story_id',
        ],
    ]) ?>

</div>
