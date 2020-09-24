<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideo */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Story Videos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-video-view">

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
            'story_id',
            'title',
            'video_url:url',
            'video_cover',
            'video_type',
            'is_show',
            'views',
            'likes',
            'share_num',
            'created_at',
        ],
    ]) ?>

</div>
