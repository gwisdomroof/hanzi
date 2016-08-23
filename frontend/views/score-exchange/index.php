<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ScoreExchange;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ScoreExchangeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Score Exchanges');
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

<div class="score-exchange-index">
    <p>
        <?= Html::a(Yii::t('frontend', '申请兑换'), ['apply'], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="sum">
    <?php 
        $totalScore = \common\models\HanziUserTask::getScore(Yii::$app->user->id);
        $changeScore = \common\models\ScoreExchange::getScore(Yii::$app->user->id);
        $leftScore = $totalScore - $changeScore;
        echo "总积分" . $totalScore . "，已用".$changeScore."分，剩余" . $leftScore . "分。" ;
    ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'userid',
            [                     
                'attribute' => 'type',
                'value' => function ($data) {
                    return empty($data['type']) ? '' : $data->types()[$data['type']]; 
                    },
                'filter'=>ScoreExchange::types(),
            ],
            'score',
            [                     
                'attribute' => 'status',
                'value' => function ($data) {
                    return empty($data['status']) ? '' : $data->statuses()[$data['status']]; 
                    },
                'filter'=>ScoreExchange::statuses(),
            ],
            'remark',
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
    ]); ?>
</div>
