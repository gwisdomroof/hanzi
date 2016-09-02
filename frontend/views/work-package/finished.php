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
    <div class="col-sm-10">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'headerRowOptions' => ['style'=>'color:#337ab7'],
            'summary' => '',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
//            'id',
//            'type',
                [
                    'attribute' => 'type',
                    'header' => '任务包',
                    'value' => function ($data) {
                        return empty($data['type']) ? '' : WorkPackage::types()[$data['type']] . '_' . $data['volume'] . '个';
                    },
                ],
                [
                    'header' => '每日计划',
                    'attribute' => 'daily_schedule',
                    "headerOptions" => ["width" => "120"]
                ],
                [
                    'header' => '已完成',
                    'attribute' => 'progress'
                ],
                [
                    'header' => '领取日',
                    'attribute' => 'created_at',
                    'format' => ['datetime', 'php:Y-m-d'],
                ],
                [
                    'header' => '完成日',
                    'attribute' => 'updated_at',
                    'format' => ['datetime', 'php:Y-m-d'],
                ],
            ],
        ]); ?>
    </div>
    <div class="col-sm-2 pull-right">
        <nav class="navbar" style="background-color: #FFF8DC; border: 1px solid #E0DCBF;">
            <ul class="nav">
                <li><a href="<?= Url::toRoute(['index']) ?>">进行中</a></li>
                <li style="background-color:#f5f5f5;"><a href="<?= Url::toRoute(['finished']) ?>">已完成</a></li>
                <li><a href="<?= Url::toRoute(['create']) ?>">领任务</a></li>
                <li><a href="<?= Url::toRoute(['detail']) ?>">详　情</a></li>
            </ul>
        </nav>
    </div>
</div>
