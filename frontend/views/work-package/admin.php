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
            'filterModel' => $searchModel,
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
                    'header' => '领取日',
                    'attribute' => 'created_at',
                    'format' => ['datetime', 'php:Y-m-d'],
                ],
                [
                    'header' => '',
                    'attribute' => 'expected_date',
                    'format' => ['datetime', 'php:Y-m-d'],
                ],
                [
                    'header' => '已完成',
                    'attribute' => 'progress',
                    "headerOptions" => ["width" => "120"],
                    'value' => function ($data) {
                        return $data->getCurrentProgress();
                    }
                ],
                [
                    'header' => '进度',
                    'attribute' => 'progress',
                    "headerOptions" => ["width" => "120"],
                    'value' => function ($data) {
                        return $data->getCurrentProgress()/$data['volume']*100 . '%';
                    }
                ],
                [
                    'header' => '操作',
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{start} {update}',
                    'buttons' => [
                        'start' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '开始工作'),
                                'aria-label' => Yii::t('yii', '开始工作'),
                            ];

                            $url = Url::toRoute(['']);
                            if ($model->type == HanziTask::TYPE_SPLIT) {
                                $url = Url::toRoute(['hanzi-split/split']);
                            } elseif ($model->type == HanziTask::TYPE_INPUT) {
                                $url = Url::toRoute(['hanzi-hyyt/index']);
                            }

                            return Html::a('<span>开始工作</span>&nbsp;', $url, $options);
                        },
                        'update' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '设置'),
                                'aria-label' => Yii::t('yii', '设置'),
                            ];
                            return Html::a('<span>设置</span>', $url, $options);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
    <div class="col-sm-2 pull-right">
        <nav class="navbar" style="background-color: #FFF8DC; border: 1px solid #E0DCBF;">
            <ul class="nav">
                <li><a href="<?= Url::toRoute(['create']) ?>">领任务</a></li>
                <li style="background-color:#fff;"><a href="<?= Url::toRoute(['index']) ?>">进行中</a></li>
                <li><a href="<?= Url::toRoute(['index','progress'=>'finish']) ?>">已完成</a></li>
                <li><a href="<?= Url::toRoute(['user-task/index']) ?>">详　情</a></li>
            </ul>
        </nav>
    </div>
</div>
