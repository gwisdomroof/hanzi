<?php

use yii\helpers\Html;
use common\models\HanziTask;
use common\models\HanziUserTask;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziUserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title =  Yii::t('frontend', HanziUserTask::types()[$type]);
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

    <p>
        <?php
        $label = '录入完成任务';
        if ($type == HanziUserTask::TYPE_COLLATE) {
            $label = '录入已完成图书校对';
        } elseif ($type == HanziUserTask::TYPE_DOWNLOAD) {
            $label = '录入已完成论文下载';
        } elseif ($type == HanziUserTask::TYPE_INPUT) {
            $label = '录入已完成异体字录入';
        }
        echo Html::a(Yii::t('frontend', $label), ['create', 'type'=> $type], ['class' => 'btn btn-primary']) 
        ?>

    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [                     
            'attribute' => 'user.username',
            'label' => '用户名',
            'filter' => \yii\jui\AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'user.username',
                'options' => ['class' => 'form-control'],
                'clientOptions' => [
                    'source' => $members,
                ],
            ]),
            'value' => function ($data) {
                $identity = \common\models\User::findIdentity($data['userid']);
                return empty($identity->username) ? $data['userid'] : $identity->username;
                }
            ],
            // 'taskid',
            'quality',
            // 'created_at',
            // 'updated_at',
            [                     
            'attribute' => 'remark',
            'value' => function ($data) {
                return mb_strlen($data['remark'], 'UTF-8') <= 10 ? $data['remark'] : mb_substr($data['remark'], 0, 10, 'UTF-8') . '…';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
