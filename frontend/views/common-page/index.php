<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CommonPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Common Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="common-page-index">

    <p>
        <?= Html::a(Yii::t('frontend', '新增'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'page',
            [
                'attribute' => 'task_type',
                'value' => function ($data) {
                    return empty($data['task_type']) ? '' : HanziTask::types()[$data['task_type']];
                },
                'filter'=>HanziTask::types()
            ],
            [
                'attribute' => 'seq',
                'value' => function ($data) {
                    return empty($data['seq']) ? '' : HanziTask::seqs()[$data['seq']];
                },
                'filter'=>HanziTask::seqs()
            ],
//            'flag',
            // 'remark',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
