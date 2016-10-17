<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\GltwDedup;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\GltwDedupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Gltw Dedups');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .gaoli {
        font-family: "Tripitaka UniCode";
        font-size: 24px;
    }

    .unicode {
        font-size: 22px;
    }
</style>

<div class="gltw-dedup-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'headerOptions' => ['width' => '80px'],
            ],
            [
                'attribute' => 'gaoli',
                'headerOptions' => ['width' => '80px'],
                'format' => 'html',
                'value' => function ($model) {
                    return "<span class='gaoli'>{$model->gaoli}</span>";
                },
            ],
            [
                'attribute' => 'unicode',
                'headerOptions' => ['width' => '80px'],
                'format' => 'html',
                'value' => function ($model) {
                    $value = $model->relation <= 2 ? $model->gaoli : $model->unicode;
                    return "<span class='unicode'>{$value}</span>";
                },
            ],
            [
                'attribute' => 'relation',
                'value' => function ($model) {
                    return empty($model->relation) ? null : GltwDedup::relations()[$model->relation];
                },
                'filter' => GltwDedup::relations()
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return !isset($model->status) ? null : GltwDedup::statuses()[$model->status];
                },
                'filter' => GltwDedup::statuses()
            ],
            'remark',
            // 'created_at',
            // 'updated_at',

            [
                'header' => '操作',
                'headerOptions' => ['width' => '100', 'style' => 'color:#337ab7'],
                'class' => 'yii\grid\ActionColumn',
                'template' => '{dedup} {check}',
                'buttons' => [
                    'dedup' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '去重'),
                            'aria-label' => Yii::t('yii', '去重'),
                            'target' => '_blank'
                        ];
                        $url = Url::toRoute(['gltw-dedup/dedup', 'id' => $key]);
                        return Html::a('<span>去重</span>&nbsp;', $url, $options);
                    },
                    'check' => !$authority ? function () {
                        return '';
                    } : function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '审查'),
                            'aria-label' => Yii::t('yii', '审查'),
                        ];
                        $url = Url::toRoute(['gltw-dedup/check', 'id' => $key]);
                        return Html::a('<span>审查</span>', $url, $options);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
