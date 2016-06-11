<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziSetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzi Sets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-set-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a(Yii::t('frontend', 'Create {modelClass}', [
    'modelClass' => 'Hanzi Set',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'source',
            'type',
            'word',
            'pic_name',
            // 'nor_var_type',
            // 'belong_standard_word_code',
            // 'standard_word_code',
            // 'position_code',
            // 'bduplicate',
            // 'duplicate_id',
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
