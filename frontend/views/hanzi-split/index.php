<?php

use common\models\HanziSplit;
use common\models\HanziSet;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\HanziSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '拆字');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'header' => '', # 取消排序
                'attribute'=>'id',
                "headerOptions" => ["width" => "30"]
            ],
            // 'hanzi_type',
            [
                'header' => '', # 取消排序
                'attribute'=>'word',
                "headerOptions" => ["width" => "50"]
            ],
            [
                'header' => '', # 取消排序
                'attribute'=>'picture',
                'value'=>function ($model) {
                    return \common\models\HanziSet::getPicturePath($model->source, $model->picture);
                },
                'format' => ['image',['width'=>'25','height'=>'25']],
                "headerOptions" => ["width" => "50"]
            ],
            [
                'header' => '', # 取消排序
                'attribute'=>'source',
                "headerOptions" => ["width" => "100"],
                'value'=>function ($model) {
                    return empty($model->source) ? null: \common\models\HanziSet::sources()[$model->source];
                },
                'filter'=>\common\models\HanziSet::sources(),
            ],
//            [
//                'header' => '', # 取消排序
//                'attribute'=>'duplicate10',
//            ],
            // 'nor_var_type',
            // 'standard_word',
            // 'position_code',
            // 'radical',
            // 'stocks',
            // 'structure',
            // 'corners',
            // 'attach',
//            [
//                'header' => '', # 取消排序
//                'attribute'=>'hard10',
//                'value'=>function ($model) {
//                    return empty($model->hard10) ? null:  HanziSet::hards()[$model->hard10];
//                },
//                'filter'=>HanziSet::hards()
//            ],
            [
                'header' => '', # 取消排序
                'label' => '初次：初步拆分',
                'attribute'=>'initial_split11',
            ],
            [
                'header' => '', # 取消排序
                'label' => '回查：初步拆分',
                'attribute'=>'initial_split21',
            ],
            [
                'header' => '', # 取消排序
                'label' => '审查：初步拆分',
                'attribute'=>'initial_split31',
            ],
            // 'initial_split12',
            // 'deform_split10',
            // 'similar_stock10',
            // 'hard20',
            // 'initial_split21',
            // 'initial_split22',
            // 'deform_split20',
            // 'similar_stock20',
            // 'hard30',
            // 'initial_split31',
            // 'initial_split32',
            // 'deform_split30',
            // 'similar_stock30',
            // 'remark',
            // 'created_at',
            // 'updated_at',

            [
                'header' => '拆字',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{first} {second} {determine} {view}',
                "headerOptions" => ["width" => "160"],
                'buttons' => [
                    'first' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '初次'),
                            'aria-label' => Yii::t('yii', '初次'),
                            'target' => '_blank'
                        ];
                        return Html::a('<span>初次</span>', $url, $options);
                    },
                    'second' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '回查'),
                            'aria-label' => Yii::t('yii', '回查'),
                            'target' => '_blank'
                        ];
                        return Html::a('<span>回查</span>', $url, $options);
                    },
                    'determine' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '审查'),
                            'aria-label' => Yii::t('yii', '审查'),
                            'target' => '_blank'
                        ];
                        return Html::a('<span>审查</span>', $url, $options);
                    },
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '查看'),
                            'aria-label' => Yii::t('yii', '查看'),
                            'target' => '_blank'
                        ];
                        return Html::a('<span>查看</span>&nbsp;', $url, $options);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
