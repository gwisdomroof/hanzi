<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\LqVariant;
use common\models\HanziSet;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LqVariantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Lq Variants');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'headerRowOptions' => ['style' => 'color:#337ab7'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'header' => '图片', # 取消排序
                'attribute'=>'ori_pic_name',
                'filter' => '',
                'value'=>function ($model) {
                    return $model->getLqPicturePath();
                },
                'format' => ['image',['width'=>'35','height'=>'35']],
                "headerOptions" => ["width" => "60"]
            ],
            [
                'attribute' => 'ori_pic_name',
//                'headerOptions' => ['width' => '150'],
            ],
            [
                'attribute' => 'pic_name',
                'headerOptions' => ['width' => '100'],
            ],
            [
                'attribute' => 'word',
                'headerOptions' => ['width' => '80'],
            ],
            [
                'attribute' => 'source',
                'headerOptions' => ['width' => '100'],
                'value' => function ($data) {
                    return empty($data->source) ? '' : LqVariant::sources()[$data->source];
                },
                'filter' => LqVariant::sources()
            ],
            // 'type',
            [
                'attribute' => 'nor_var_type',
                'headerOptions' => ['width' => '120'],
                'value' => function ($data) {
                    return empty($data->nor_var_type) ? '' : HanziSet::norVarTypes()[$data->nor_var_type];
                },
                'filter' => HanziSet::norVarTypes()
            ],
            'belong_standard_word_code',
            // 'standard_word_code',
            // 'position_code',
            // 'duplicate',
            // 'duplicate_id',
            // 'frequence',
//             'sutra_ids',
//            'bconfirm',
            // 'pinyin',
            // 'radical',
            // 'stocks',
            // 'zhengma',
            // 'wubi',
            // 'structure',
            // 'bhard',
            // 'min_split',
            // 'deform_split',
            // 'similar_stock',
            // 'max_split',
            // 'mix_split',
            // 'stock_serial',
            // 'remark',
            // 'created_at',
            // 'updated_at',
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '{view} {update}'
//            ],
        ],
    ]); ?>
</div>
