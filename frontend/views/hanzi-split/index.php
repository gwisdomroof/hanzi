<?php

use common\models\HanziSplit;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziSearch */
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
                'value'=>function ($model) {
                    return empty($model->source) ? null: HanziSplit::sources()[$model->source];
                },
                'filter'=>HanziSplit::sources(),
            ],
            [
                'header' => '', # 取消排序
                'attribute'=>'duplicate10',
            ],
            // 'nor_var_type',
            // 'standard_word',
            // 'position_code',
            // 'radical',
            // 'stocks',
            // 'structure',
            // 'corners',
            // 'attach',
            [
                'header' => '', # 取消排序
                'attribute'=>'hard10',
                'value'=>function ($model) {
                    return empty($model->hard10) ? null:  \common\models\HanziSplit::hards()[$model->hard10];
                },
                'filter'=>HanziSplit::hards()
            ],
            [
                'header' => '', # 取消排序
                'label' => '初步拆分',
                'attribute'=>'initial_split11',
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
                'header' => '操作',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '查看'),
                            'aria-label' => Yii::t('yii', '查看'),
                        ];
                        return Html::a('<span>查看</span>&nbsp;', $url, $options);
                    },
                    'update' => !$authority ? function () {return '';} : function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '拆字'),
                            'aria-label' => Yii::t('yii', '拆字'),
                        ];
                        return Html::a('<span>拆字</span>', $url, $options);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
