<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\user;
use common\models\MemberRelation;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Member Relations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-relation-index">


    <p>
        <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Member Relation',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            [                     
            'label' => '组长',
            'value' => function ($data) {
                return User::findIdentity($data['leader_id'])->username; 
                },
            ],
            [                     
            'label' => '拆字员',
            'value' => function ($data) {
                return User::findIdentity($data['member_id'])->username; 
                },
            ],
            [                     
            'label' => '状态',
            'value' => function ($data) {
                return MemberRelation::statuses()[$data['status']]; 
                },
            ],
            'remark',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
