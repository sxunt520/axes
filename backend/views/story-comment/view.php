<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\StoryComment */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '故事评论列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-comment-view">

    <p>
        <?php // Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
//        Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ])
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'story_id',
            //'comment_type',
            'title',
            'content:ntext',
            'from_uid',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            'comment_img_id',
            'heart_val',
            'likes',
            'views',
            'share_num',
            'is_plot',
            'is_show',
            'is_choiceness',
            'is_top',
            //'status',
            //'update_at',
        ],
    ]) ?>

</div>
