<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GltwDedupResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Gltw Dedup Results');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gltw-dedup-result-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Gltw Dedup Result'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
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
            // 'duplicate_id1',
            // 'duplicate_id2',
            // 'duplicate_id3',
            // 'remark',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
