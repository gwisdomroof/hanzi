<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-task-view">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <div class="col-lg-offset-1 col-lg-10">
    <p>
        <?= Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?> -->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [                      
            'label' => '姓名',
            'value' => User::findIdentity($model->user_id)->username,
            ],
            [                      
            'label' => '组长',
            'value' => User::findIdentity($model->leader_id)->username,
            ],
            'page',
            [                      
            'label' => '次数',
            'value' => HanziTask::seqs()[$model->seq],
            ],
            'start_id',
            'end_id',
            [                      
            'label' => '状态',
            'value' => HanziTask::statuses()[$model->status],
            ],
            'remark',
            // [                      
            // 'attribute' => 'created_at',
            // 'format'=>['datetime','php:Y-m-d H:i:s'],
            // ],
            // [                      
            // 'attribute' => 'updated_at',
            // 'format'=>['datetime','php:Y-m-d H:i:s'],
            // ],
        ],
    ]) ?>
    
    </div>
</div>
