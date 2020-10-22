<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create Member', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box box-primary">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    [
                        "attribute" => "id",
                        "value" => "id",
                        "headerOptions" => ["width" => "60"],
                    ],
                    [
                        "attribute" => "username",
                        "value" => "username",
                        "headerOptions" => ["width" => "200"],
                    ],
                    //'auth_key',
                    //'password_hash',
                    //'password_reset_token',
                    // 'email:email',
                    // 'status',
                    // 'is_admin',
                    // 'updated_at',
                    // 'access_token',
                    // 'allowance',
                    // 'allowance_updated_at',
                    // 'api_token',
                    [
                        "attribute" => "mobile",
                        "value" => "mobile",
                        "headerOptions" => ["width" => "200"],
                    ],
                     //'picture_url:url',
                    [
                        'label' => '用户头像',
                        'format' => [
                            'image',
                            [
                                'width'=>'50',
                                'height'=>'auto'
                            ]
                        ],
                        'value' => function ($model) {
                            $pic=strpos($model->picture_url, 'http') === false ? (\Yii::getAlias('@static') . $model->picture_url) : $model->picture_url;
                            return $pic;
                        }
                    ],
                     'nickname',
                    // 'signature',
                    // 'real_name',
                    // 'real_idCard',
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],
                    //'real_name_status',
                    [
                        "attribute" => "real_name_status",
                        "value" => function ($model) {
                            return backend\models\MemberSearch::dropDown("real_name_status", $model->real_name_status);
                        },
                        "filter" =>  backend\models\MemberSearch::dropDown("real_name_status"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],
                    //['class' => 'yii\grid\ActionColumn'],
                        [
                            "class" => "yii\grid\ActionColumn",
                            //"template" => "{get-xxx} {view} {update}",
                            "template" => "{update}",
                            "header" => "实名认证操作",
                            "buttons" => [
                                "update" => function ($url, $model, $key) {
                                    return Html::a("审核", $url, ["title" => "审核"] );
                                },
                            ],
                        ],
                ],
            ]); ?>
        </div>
    </div>

</div>
