<?php

use common\models\HanziSplit;
use common\models\HanziSet;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\HanziSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '处理去重结果');
$this->params['breadcrumbs'][] = $this->title;
?>
    <style>
        .duplicate10text, .duplicate20text, .duplicate30 {
            font-size: 35px;
        }
    </style>

    <div class="hanzi-index col-sm-10 col-sm-offset-1">

        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' '],
            'columns' => [
                // ['class' => 'yii\grid\SerialColumn'],
                [
                    'header' => '', # 取消排序
                    'attribute' => 'id',
                    "headerOptions" => ["width" => "30"]
                ],
                [
                    'header' => '', # 取消排序
                    'attribute' => 'picture',
                    'value' => function ($model) {
                        return \common\models\HanziSet::getPicturePath($model->source, $model->picture);
                    },
                    'format' => ['image', ['width' => '35', 'height' => '35']],
                    "headerOptions" => ["width" => "50"]
                ],
                [
                    'header' => '重复值1',
                    'attribute' => 'duplicate10',
                    'headerOptions' => ['width' => '80'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return "<div class='duplicate10text' id='duplicate10{$model->id}'>$model->duplicate10</div>";
                    },
                ],
                [
                    'header' => '重复值2',
                    'attribute' => 'duplicate20',
                    'headerOptions' => ['width' => '80'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return "<div class='duplicate20text' id='duplicate20{$model->id}'>$model->duplicate20</div>";
                    },
                ],
                [
                    'header' => '重复值1',
                    'attribute' => 'duplicate10',
                    'format' => 'raw',
                    'filter' => ['1' => '是', '0' => '否'],
                    'headerOptions' => ['width' => '120'],
                    'value' => function ($model) {
                        $selected = false;
                        if ($model->duplicate10 == $model->duplicate30 && !empty($model->duplicate10))
                            $selected = true;
                        return Html::radioList('duplicate10' . $model->id, $selected, ['1' => '是', '0' => '否'], ['class' => 'duplicate10', 'id' => $model->id]);
                    },
                ],
                [
                    'header' => '重复值2',
                    'attribute' => 'duplicate20',
                    'format' => 'raw',
                    'filter' => ['1' => '是', '0' => '否'],
                    'headerOptions' => ['width' => '120'],
                    'value' => function ($model) {
                        $selected = false;
                        if ($model->duplicate20 == $model->duplicate30 && !empty($model->duplicate20))
                            $selected = true;
                        return Html::radioList('duplicate20' . $model->id, $selected, ['1' => '是', '0' => '否'], ['class' => 'duplicate20', 'id' => $model->id]);
                    },
                ],

                [
                    'header' => '重复值3',
                    'attribute' => 'duplicate30',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return "<div class='duplicate30' id='duplicate30{$model->id}'>$model->duplicate30</div>";
                    },
                ],
            ],
        ]); ?>

    </div>


<?php
$script = <<<SCRIPT
    $(document).on('click', '.duplicate10', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var text = $('#duplicate10'+id).text();
        var radioListName = 'duplicate10' + id;
        var value = $('input[name="' + radioListName + '"]:checked').val();
        $.post( {
            url: "/hanzi-split/check?field=duplicate10&id=" + id,
            data: {value: value},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    obj.fadeOut("fast");
                    obj.fadeIn("fast");
                    $('#duplicate30'+id).text(text);
                    return true;
                } else {
                    alert('失败！');
                }
            },
            error: function(result) {
                alert(result.msg)
            }
        });
    });
    
    $(document).on('click', '.duplicate20', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var radioListName = 'duplicate20' + id;
        var value = $('input[name="' + radioListName + '"]:checked').val();
        var text = $('#duplicate20'+id).text();
        $.post( {
            url: "/hanzi-split/check?field=duplicate20&id=" + id,
            data: {value: value},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    obj.fadeOut("fast");
                    obj.fadeIn("fast");
                    $('#duplicate30'+id).text(text);
                    return true;
                } else {
                }
            },
            error: function(result) {
                alert(result.msg)
            }
        });
    });
   

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);