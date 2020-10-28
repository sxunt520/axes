<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-view">

    <p>
        <?= Html::a('审核认证状态', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /* Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            //'email:email',
            //'status',
            //'is_admin',
            //'created_at',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            //'updated_at',
            //'access_token',
            //'allowance',
            //'allowance_updated_at',
            //'api_token',
            'mobile',
            'picture_url:url',
//            [
////                'attribute' => 'picture_url',
////                'label' => '用户头像',
////                'format' => ['image',['width'=>'40','height'=>'40',]],//这个不确定能不能用
////                //'value'  => Html::a(Html::img($model->picture_url),$model->picture_url),
////                'value' => function($model) {
////                    //$sex = ['保密', '男', '女'];
////                    //return '1111';
////                }
////            ],
//
////            [
////                'label' => '用户头像',
////                'format' => [
////                    'image',
////                    [
////                        'width'=>'50',
////                        'height'=>'auto'
////                    ]
////                ],
////                'value' => function ($model) {
////                    $pic=strpos($model->picture_url, 'http') === false ? (\Yii::getAlias('@static') . $model->picture_url) : $model->picture_url;
////                    return $pic;
////                }
////            ],

            'nickname',
            'signature',
            'real_name_status',
//            [
//                'attribute' => 'real_name_status',
//                'format'=>'raw',
//                'value' =>  function ($model) {
//                    return $model->owner->name;
//                },
//            ],


            'real_name',
            'real_idCard',
        ],
    ]) ?>

</div>
