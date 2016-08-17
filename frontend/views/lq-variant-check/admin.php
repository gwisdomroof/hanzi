<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LqVariantCheckSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Lq Variant Checks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-check-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Lq Variant Check'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'source',
            'pic_name',
            'variant_code1',
            'belong_standard_word_code1',
            // 'nor_var_type1',
            // 'level1',
            // 'variant_code2',
            // 'belong_standard_word_code2',
            // 'nor_var_type2',
            // 'level2',
            // 'bconfirm',
            // 'remark',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
