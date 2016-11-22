<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GltwDedupResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Gltw Dedup Results');
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="application/javascript">
    function updateGoryeoVariantExpertCheck(url, key, choice) {
        console.log(key);
        $.get(window.location.origin + url + '&choice=' + choice, function (data, status) {
            console.log(data + ' ' + status);
            if (status) $("tr[data-key="+key+"]").css('backgroundColor', 'beige');
        });
    }
</script>
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
            [
                'attribute'=> 'source',
                'value'=>function($model){
                    return \common\models\HanziSet::sources()[$model->source];
                }
            ],
            [
                'attribute'=>'type',
                'value'=>function($model){
                  if ($model->type) return \common\models\HanziSet::norVarTypes()[$model->type];
                    return '';
                }
            ],
            'word',
            [
                'attribute'=>'Origin Character',
                'format'=> 'raw',
                'value'=> function($model){
                    return Html::img(\common\models\HanziSet::getPicturePath($model->source, $model->pic_name));
                }
            ],
            [
                'attribute'=>'duplicate_id1',
                'format'=> 'html',
                'value'=> function($model) {
                    return '<span style="font-size: 36px">'.$model->duplicate_id1.'</span>';
                }
            ],
            [
                'attribute'=>'duplicate_id2',
                'format'=> 'html',
                'value'=> function($model) {
                    return '<span style="font-size: 36px">'.$model->duplicate_id2.'</span>';
                }
            ],
            [
                'class'=>'yii\grid\ActionColumn',
                'header'=>'专家判断',
                'template'=>'{judge}',
                'buttons'=> [
                    'judge'=>function($url, $model, $key) {

                        return Html::a('选择1', 'javascript:void(0);', ['onclick'=>'updateGoryeoVariantExpertCheck("'.$url.'",'.$key.',1)']).'<br/>'
                            . Html::a('选择2', 'javascript:void(0);', ['onclick'=>'updateGoryeoVariantExpertCheck("'.$url.'",'.$key.',2)']) .'<br/>'
                            . Html::a('都不对', 'javascript:void(0);', ['onclick'=>'updateGoryeoVariantExpertCheck("'.$url.'",'.$key.',0)']);
                    },
                ],
            ],

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
