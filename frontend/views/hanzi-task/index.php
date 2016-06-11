<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '我的任务');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-task-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= GridView::widget([
        'layout'=>"{summary}\n{items}\n{pager}",
        'summary' => "您共有{totalCount}页任务，当前为{begin}至{end}页。",
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'id',
                "headerOptions" => ["width" => "30"]
            ],
            'member.username',
            'leader.username',
            [                     
            'attribute' => 'seq',
            'value' => function ($data) {
                return empty($data['seq']) ? '' : $data->seqs()[$data['seq']]; 
                },
            'filter'=>HanziTask::seqs(),
            ],
            [                     
            'attribute' => 'page',
            'value' => function ($data) {
                return empty($data['page']) ? '' : Html::a($data['page'],  yii\helpers\Url::to(['hanzi-split/index', 'page' => $data->page], true));
                },
            'format' => 'raw',
            ],
            'start_id',
            'end_id',
            [                     
            'attribute' => 'status',
            'value' => function ($data) {
                return !isset($data['status']) ? '' : $data->statuses()[$data['status']]; 
                },
            'filter'=>HanziTask::statuses(),
            ],
            // 'remark',
            // 'created_at',
            // 'updated_at',

            [
                'header' => '操作',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                "headerOptions" => ["width" => "100"]
            ],
        ],
    ]); ?>
</div>
