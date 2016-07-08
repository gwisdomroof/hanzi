<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\HanziUserTask */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi User Tasks')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-user-task-view">


    <p>
        <?= Html::a(Yii::t('frontend', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            [                      
            'label' => '姓名',
            'value' => \common\models\User::findIdentity($model->userid)->username,
            ],   
            // 'taskid',
            // 'task_type',
            [                      
            'label' => '任务类型',
            'value' => \common\models\HanziUserTask::types()[$model->task_type],
            ],
            [                      
            'label' => '阶段',
            'value' => \common\models\HanziTask::seqs()[$model->task_seq],
            ],
            // 'task_status',
            'quality',
            // 'created_at',
            // 'updated_at',
        ],
    ]) ?>

</div>
