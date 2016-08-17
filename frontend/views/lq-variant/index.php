<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LqVariantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Lq Variants');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Lq Variant'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'source',
            'pic_name',
            'variant_code',
            'belong_standard_word_code',
            // 'nor_var_type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
