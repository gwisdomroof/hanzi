<?php

use yii\helpers\Html;
use common\models\HanziUserTask;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziUserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '积分排名');
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
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [                     
                'attribute' => 'user.username',
                'header' => '<span style="color:#337ab7;">用户名</span>',
                'value' => function ($data) {
                    $username = $data['user']['username'];
                    if (!\common\models\HanziTask::isLeader(Yii::$app->user->id)) {
                        $len = mb_strlen($username);
                        $username = mb_substr($username, 0, 1, 'UTF-8');
                        for ($i=1; $i < $len; $i++) { 
                            $username = $username . '＊';
                        }
                    }
                    return $username; 
                },
            ],
            [                     
                'attribute' => 'task_type',
                'header' => '<span style="color:#337ab7;">任务类型</span>',
                'value' => function ($data) {
                    return empty($data['task_type']) ? '总积分' : HanziUserTask::types()[$data['task_type']]; 
                },
                'filter'=>HanziUserTask::types(),
            ],
            'cnt',
            // 'task_status',
            // 'quality',
            // 'created_at',
            // 'updated_at',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
