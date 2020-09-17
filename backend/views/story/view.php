<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Stories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'title',
            'intro:ntext',
            'type',
            'cover_url:url',
            'video_url:url',
            'created_at',
            'updated_at',
            'admin_id',
            'next_updated_at',
            'current_chapters',
            'total_chapters',
            'is_show',
            'likes',
            'views',
            'share_num',
            'record_pic',
            'game_title',
        ],
    ]) ?>

</div>
