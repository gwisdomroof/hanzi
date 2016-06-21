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
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            [                     
            'attribute' => 'leader_id',
            'value' => function ($data) {
                return User::findIdentity($data['leader_id'])->username; 
                },
            'filter'=>MemberRelation::leaders()
            ],
            [                     
            'attribute' => 'member_id',
            'value' => function ($data) {
                return User::findIdentity($data['member_id'])->username; 
                },
            'filter'=>MemberRelation::members()
            ],
            [                     
            'attribute' => 'relation_type',
            'value' => function ($data) {
                    if (!empty($data['relation_type'])) {
                        return MemberRelation::types()[$data['relation_type']]; 
                    }
                },
            'filter'=>MemberRelation::types()
            ],
            [                     
            'attribute' => 'status',
            'value' => function ($data) {
                return MemberRelation::statuses()[$data['status']]; 
                },
            'filter'=>MemberRelation::statuses()
            ],
            'remark',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
