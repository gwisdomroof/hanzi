<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\MemberRelation;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\MemberRelation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Member Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-relation-view">

    <p>
        <?php echo Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [                      
            'label' => '组长',
            'value' => User::findIdentity($model->leader_id)->username,
            ],
            [                      
            'label' => '拆字员',
            'value' => User::findIdentity($model->member_id)->username,
            ],
            [                      
            'label' => '状态',
            'value' => MemberRelation::statuses()[$model->status],
            ],
            'remark',
            [                      
            'attribute' => 'created_at',
            'format'=>['datetime','php:Y-m-d H:i:s'],
            ],
            [                      
            'attribute' => 'updated_at',
            'format'=>['datetime','php:Y-m-d H:i:s'],
            ],
        ],
    ]) ?>

</div>
