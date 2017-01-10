<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\WorkPackage;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $searchModel common\models\WorkPackageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Work Packages');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="work-package-index">
    <div class="col-sm-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'headerRowOptions' => ['style' => 'color:#337ab7'],
            'summary' => '',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                [
                    'attribute' => 'created_at',
                    'format' => ['datetime', 'php:Y-m-d'],
                    "headerOptions" => ["width" => "100"],
                    'filter' => ''
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => ['datetime', 'php:Y-m-d'],
                    "headerOptions" => ["width" => "100"],
                    'filter' => ''
                ],
                [
                    'attribute' => 'user.username',
                ],
                [
                    'attribute' => 'type',
                    'value' => function ($data) {
                        return WorkPackage::types()["$data->type"];
                    },
                    'filter' => WorkPackage::types()
                ],
                [
                    'attribute' => 'volume',
                ],
                [
                    'header' => '每日计划',
                    'attribute' => 'daily_schedule',
                ],
                [
                    'header' => '今日完成',
                    'attribute' => 'progress',
                    "headerOptions" => ["width" => "100"],
                    'filter' => '',
                    'value' => function ($data) {
                        return $data->getFinishedToday();
                    }
                ],
                [
                    'header' => '总完成',
                    'attribute' => 'progress',
                    'filter' => '',
                    "headerOptions" => ["width" => "80"],
                    'value' => function ($data) {
                        return $data->getCurrentProgress();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    "headerOptions" => ["width" => "80"],
                ],
            ],
        ]); ?>
    </div>
</div>
