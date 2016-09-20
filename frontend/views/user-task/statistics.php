<?php

use yii\helpers\Html;
use common\models\HanziTask;
use common\models\HanziUserTask;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\HanziUserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Tasks Statistics');
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .hasDatepicker {
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
    }
</style>

<div class="hanzi-user-task-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // 'id',
//             'userid',
            'user.username',
            [
                'attribute' => 'task_type',
                'value' => function ($data) {
                    return empty($data['task_type']) ? '' : HanziTask::types()[$data['task_type']];
                },
                'filter' => HanziTask::types(),
            ],
            'taskid',
            [
                'label' => '任务时间',
                'attribute' => 'updated_at',
                'format' => ['datetime', 'php:Y-m-d H:i:s'],
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'language' => 'zh-CN',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
            ],

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
