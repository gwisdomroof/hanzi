<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\WorkClockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Work Clocks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-clock-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:Y-m-d'],
                "headerOptions" => ["width" => "15%"],
                'filter' => ''
            ],
            [
                'attribute' => 'type',
                "headerOptions" => ["width" => "15%"],
                'value' => function ($data) {
                    return \common\models\WorkPackage::types()["{$data['type']}"];
                },
                'filter' => \common\models\WorkPackage::types()
            ],
            [
                'attribute' => 'user.username',
            ],
            'content',
//             'updated_at',
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template'=> '{update}',
//                "headerOptions" => ["width" => "50px"]
//            ],
        ],
    ]); ?>
</div>
