<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\ScoreExchange;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ScoreExchangeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Score Exchanges');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-exchange-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'userid',
            [
                'attribute' => 'user.username',
                'filter'=>Html::activeTextInput($searchModel, 'user.username',['class'=>'form-control'])
            ],
            [                     
                'attribute' => 'type',
                'value' => function ($data) {
                    return empty($data['type']) ? '' : $data->types()[$data['type']]; 
                    },
                'filter'=>ScoreExchange::types(),
            ],
            [          
                // 'header' => '',           
                'attribute' => 'score',
            ],
            [                     
                'attribute' => 'status',
                'value' => function ($data) {
                    return empty($data['status']) ? '' : $data->statuses()[$data['status']]; 
                    },
                'filter'=>ScoreExchange::statuses(),
            ],
            'remark',
            // [                      
            //     'attribute' => 'created_at',
            //     'format'=>['datetime','php:Y-m-d H:i:s'],
            // ],
            // [                      
            //     'attribute' => 'updated_at',
            //     'format'=>['datetime','php:Y-m-d H:i:s'],
            // ],

            [
                // 'header' => '操作',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $url = Url::toRoute(['score-exchange/approve', 'id'=>$key]);
                        $options = [
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
