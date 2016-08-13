<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '用户');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'username',
            // 'auth_key',
            // 'access_token',
            // 'password_hash',
            // 'oauth_client',
            // 'oauth_client_user_id',
            'email:email',
            // 'status',
            [                      
                'attribute' => 'created_at',
                'format'=>['datetime','php:Y-m-d H:i:s'],
            ],
            [                      
                'attribute' => 'updated_at',
                'format'=>['datetime','php:Y-m-d H:i:s'],
            ],

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
