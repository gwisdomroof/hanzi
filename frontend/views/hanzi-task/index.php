<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = '任务列表';
?>

<div class="hanzi-task-index">
    <?= GridView::widget([
        'layout'=>"{summary}\n{items}\n{pager}",
        'summary' => "您共有{totalCount}个任务，当前为第{begin}至{end}个。",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                "headerOptions" => ["width" => "30"]
            ],
            [
                'attribute' => 'task_type',
                "headerOptions" => ["width" => "100"],
                'value' => function ($data) {
                    return empty($data['task_type']) ? '' : $data->types()[$data['task_type']];
                },
                'filter'=>HanziTask::types()
            ],
            'leader.username',
//            [
//                'attribute' => 'seq',
//                'value' => function ($data) {
//                    return empty($data['seq']) ? '' : $data->seqs()[$data['seq']];
//                },
//                'filter'=>HanziTask::seqs(),
//                "headerOptions" => ["width" => "120"]
//            ],
            [
                'attribute' => 'page',
                'value' => function ($data) {
                    $url = $data->task_type == HanziTask::TYPE_SPLIT ? 'hanzi-split/index' : 'hanzi-hyyt/index';
                    return empty($data['page']) ? '' : Html::a($data['page'],  yii\helpers\Url::to([$url, 'page' => $data->page], true));
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return !isset($data['status']) ? '' : $data->statuses()[$data['status']];
                },
                'filter'=>HanziTask::statuses(),
                "headerOptions" => ["width" => "120"]
            ],
//            [
//                'header' => '操作',
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{view}',
//                "headerOptions" => ["width" => "120"],
//                'buttons' => [
//                    'view' => function ($url, $data, $key) {
//                        $options = [
//                            'title' => Yii::t('yii', '查看任务'),
//                            'aria-label' => Yii::t('yii', '查看任务'),
//                        ];
//                        $route = $data->task_type == HanziTask::TYPE_SPLIT ? 'hanzi-split/index' : 'hanzi-hyyt/index';
//                        $url = yii\helpers\Url::to([$route, 'page' => $data->page]);
//                        return Html::a('<span>查看</span>&nbsp;', $url, $options);
//                    },
//                    'update' => function ($url, $data, $key) {
//                        $options = [
//                            'title' => Yii::t('yii', '更新状态'),
//                            'aria-label' => Yii::t('yii', '更新状态'),
//                        ];
//                        $url = yii\helpers\Url::to(['hanzi-task/update', 'id' => $data->id]);
//                        return Html::a('<span>更新状态</span>&nbsp;', $url, $options);
//                    },
//                ],
//            ],
        ],
    ]); ?>
</div>
