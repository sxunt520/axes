<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideoTopic */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '视频主题列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-video-topic-view">

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
            'story_id',
            'topic_title',
            'topic_cover',
            'content:ntext',
            'is_show',
            'created_at',
        ],
    ]) ?>

</div>
