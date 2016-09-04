<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\HanziSet;
use common\models\LqVariantCheck;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LqVariantCheckSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Lq Variant Checks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-check-index">
    <p style="margin-bottom: 5px;">
        <?= Html::a(Yii::t('frontend', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'headerRowOptions' => ['style' => 'color:#337ab7'],
        'columns' => [
//            'id',
//            'userid',
            [
                'header' => '图片', # 取消排序
                'attribute'=>'pic_name',
                'filter' => '',
                'value'=>function ($model) {
                    return $model->getPicPath();
                },
                'format' => ['image',['width'=>'35','height'=>'35']],
                "headerOptions" => ["width" => "60"]
            ],
            [
                'attribute' => 'created_at',
                'filter' => '',
                'headerOptions' => ['width' => '100'],
                'format' => ['datetime', 'php:Y-m-d'],
            ],
            [
                'attribute' => 'user.username',
                'headerOptions' => ['width' => '100'],
            ],
            [
                'attribute' => 'source',
                'headerOptions' => ['width' => '80'],
                'value' => function ($data) {
                    return empty($data->source) ? '' : LqVariantCheck::sources()[$data->source];
                },
                'filter' => LqVariantCheck::sources()
            ],
            'pic_name',
            'variant_code',
            // 'origin_standard_word_code',
            'belong_standard_word_code',
            [
                'attribute' => 'nor_var_type',
                'headerOptions' => ['width' => '120'],
                'value' => function ($data) {
                    return empty($data->nor_var_type) ? '' : HanziSet::norVarTypes()[$data->nor_var_type];
                },
                'filter' => HanziSet::norVarTypes(),
            ],
            [
                'attribute' => 'level',
                'headerOptions' => ['width' => '80'],
                'value' => function ($data) {
                    return empty($data->level) ? '' : LqVariantCheck::levels()[$data->level];
                },
                'filter' => LqVariantCheck::levels()
            ],
            [
                'attribute' => 'bconfirm',
                'headerOptions' => ['width' => '80'],
                'value' => function ($data) {
                    $arr = [1 => '是', 0 => '否'];
                    return !isset($data->bconfirm) ? '' : $arr[$data->bconfirm];
                },
                'filter' => [1 => '是', 0 => '否']
            ],
            // 'remark',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => '80'],
                'template' => '{view}  {update}'
            ],
        ],
    ]); ?>
</div>
