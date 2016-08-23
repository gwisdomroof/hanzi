<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\HanziHyytSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzi Hyyts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-hyyt-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Hanzi Hyyt'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'volume',
            'page',
            'num',
            'picture',
            // 'word1',
            // 'type1',
            // 'tong_word1',
            // 'zhushi1',
            // 'word2',
            // 'type2',
            // 'tong_word2',
            // 'zhushi2',
            // 'word3',
            // 'type3',
            // 'tong_word3',
            // 'zhushi3',
            // 'remark',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
