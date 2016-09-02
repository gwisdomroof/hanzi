<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$taskName = $type == 1 ? '拆字' : '录入';
$this->title = Yii::t('frontend', $taskName. "任务管理");
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="hanzi-task-index">
    <p>
<!--        --><?php //if (\common\models\HanziTask::isLeader(Yii::$app->user->id))
//           echo Html::a(Yii::t('frontend', "创建" . $taskName . "任务"), ['create', 'type'=>$type], ['class' => 'btn btn-primary'])
//        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
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
                $url = $data->task_type == HanziTask::TYPE_SPLIT ? 'hanzi-split/index' : 'hanzi-hyyt/index';
                return empty($data['page']) ? '' : Html::a($data['page'],  yii\helpers\Url::to([$url, 'page' => $data->page], true));
                },
            'format' => 'raw',
            ],
            [
            'attribute' => 'start_id',
            'visible' => $type == 1 ? true : false,
            ],
            [
            'attribute' => 'end_id',
            'visible' => $type == 1 ? true : false,
            ],
            [                     
            'attribute' => 'status',
            'value' => function ($data) {
                return !isset($data['status']) ? '' : $data->statuses()[$data['status']]; 
                },
            'filter'=>HanziTask::statuses(),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                "headerOptions" => ["width" => "100"],
                'template' => '{view}{update}'
            ]
        ],
    ]); ?>

</div>
