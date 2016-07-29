<?php

use yii\helpers\Html;
use common\models\HanziTask;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziUserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'My Completed Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .sum {
        color: #cc0000;
        margin: 5px 0px;
    }
    .summary {
        display: none;
    }

</style>

<div class="hanzi-user-task-index">
    <div class="sum">
        <?php 
            $totalNum = \common\models\HanziUserTask::getScore(Yii::$app->user->id);
            echo "已完成拆字{$splitNum}条，录入{$inputNum}条，总计积分{$totalNum}分。";
        ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'userid',
            // 'task_type',
            [                     
                'attribute' => 'task_type',
                'value' => function ($data) {
                    return empty($data['task_type']) ? '' : HanziTask::types()[$data['task_type']]; 
                    },
                'filter'=>HanziTask::types(),
            ],
            // 'taskid',
            [                     
                'attribute' => 'taskid',
                'value' => function ($data) {
                    if ($data->task_type == HanziTask::TYPE_SPLIT) {
                        $url = 'hanzi-split/update'; 
                        return Html::a($data['taskid'],  yii\helpers\Url::to([$url, 'id' => $data['taskid']], true), ['target' => '_blank']);
                    } else {
                        $url = 'hanzi-hyyt/update';
                        return Html::a($data['taskid'],  yii\helpers\Url::to([$url, 'id' => $data['taskid']], true), ['target' => '_blank']);
                    }
                },
                'format' => 'raw',
            ],
            // 'task_seq',
            'quality',
            [                     
                'attribute' => 'task_seq',
                'value' => function ($data) {
                    return empty($data['task_seq']) ? '' : HanziTask::seqs()[$data['task_seq']]; 
                    },
                'filter'=>HanziTask::seqs(),
            ],
            // 'task_status',
            // 'created_at',
            // 'updated_at',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
