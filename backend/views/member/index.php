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
                    'id',
                    'username',
                    //'auth_key',
                    //'password_hash',
                    //'password_reset_token',
                    // 'email:email',
                    // 'status',
                    // 'is_admin',
                     'created_at',
                    // 'updated_at',
                    // 'access_token',
                    // 'allowance',
                    // 'allowance_updated_at',
                    // 'api_token',
                     'mobile',
                     'picture_url:url',
                     'nickname',
                    // 'signature',
                     'real_name_status',
                    // 'real_name',
                    // 'real_idCard',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

</div>
