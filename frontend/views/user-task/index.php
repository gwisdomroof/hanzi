<?php

use yii\helpers\Html;
use common\models\HanziTask;
use common\models\HanziUserTask;
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
            $msg = "积分情况：";
            $totalScore = 0;
            foreach ($groupScore as $item) {
                switch ($item['task_type']) {
                    case HanziUserTask::TYPE_SPLIT:
                        $msg .= "异体字拆字". $item['score'] . "分，";
                        $totalScore += (int)$item['score'];
                        break;
                    case HanziUserTask::TYPE_INPUT:
                        $msg .= "异体字录入". $item['score'] . "分，";
                        $totalScore += (int)$item['score'];
                        break;
                    case HanziUserTask::TYPE_COLLATE:
                        $msg .= "图书校对". $item['score'] . "分，";
                        $totalScore += (int)$item['score'];
                        break;
                    case HanziUserTask::TYPE_DOWNLOAD:
                        $msg .= "论文下载". $item['score'] . "分，";
                        $totalScore += (int)$item['score'];
                        break;
                }
            }
            echo $msg . "合计" . $totalScore . "分。";
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
