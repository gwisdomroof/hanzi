<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LqVariantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Lq Variants');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'pic_name',
            'source',
            // 'type',
            // 'word',
            'nor_var_type',
            'belong_standard_word_code',
            // 'standard_word_code',
            // 'position_code',
            // 'duplicate',
            // 'duplicate_id',
            // 'frequence',
            // 'sutra_ids',
            'bconfirm',
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
