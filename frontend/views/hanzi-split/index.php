<?php

use common\models\Hanzi;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzis');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <!-- <?php echo Html::a(Yii::t('frontend', 'Create {modelClass}', [
            'modelClass' => 'Hanzi',
        ]), ['create'], ['class' => 'btn btn-success']) ?> -->
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'id',
                "headerOptions" => ["width" => "30"]
            ],
            // 'hanzi_type',
            [
                'attribute'=>'word',
                "headerOptions" => ["width" => "50"]
            ],
            [
                'attribute'=>'picture',
                'value'=>function ($model) {
                    return "@web/img/tw/$model->picture";
                },
                'format' => ['image',['width'=>'25','height'=>'25']],
                "headerOptions" => ["width" => "50"]
            ],
            [
                'attribute'=>'source',
                'value'=>function ($model) {
                    return Hanzi::sources()[$model->source];
                },
                'filter'=>Hanzi::sources(),
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
                'attribute'=>'hard10',
                'value'=>function ($model) {
                    return \common\models\Hanzi::hards()[$model->hard10];
                },
                'filter'=>Hanzi::hards()
            ],
            [
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>

</div>
