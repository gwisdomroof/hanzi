<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '任务管理');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-task-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <p>
        <?php if (\common\models\HanziTask::isLeader(Yii::$app->user->id))
           echo Html::a(Yii::t('frontend', 'Create Hanzi Task'), ['create'], ['class' => 'btn btn-success']) 
        ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                "headerOptions" => ["width" => "30"]
            ],
            // [
            //     'attribute'=>'id',
            //     "headerOptions" => ["width" => "30"]
            // ],
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
                "headerOptions" => ["width" => "100"]
            ],
        ],
    ]); ?>
</div>
