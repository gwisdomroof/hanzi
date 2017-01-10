<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziPartsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzi Parts');
$this->params['breadcrumbs'][] = $this->title;
?>
    <style>
        .part_char {
            font-size: 32px;
        }

        .yes {
            color: green;
        }

        .no {
            color: red;
        }

        .empty {
            color: grey;
        }
    </style>


    <p>
        <?= Html::a(Yii::t('frontend', '批量添加'), ['add'], ['class' => 'btn btn-primary', 'target' => '_blank']) ?>
    </p>

    <div class="hanzi-parts-index">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'headerOptions' => ['width' => '60'],
                ],
//            'part_type',
                [
                    'attribute' => 'part_char',
                    'format' => 'raw',
                    'headerOptions' => ['width' => '80'],
                    'value' => function ($model) {
                        return "<div class='part_char'>{$model->part_char}</div>";
                    },
                ],
                [
                    'attribute' => 'frequency_zhzk',
                    'headerOptions' => ['width' => '60'],
                ],
                [
                    'attribute' => 'frequency',
                    'headerOptions' => ['width' => '60'],
                ],
                [
                    'attribute' => 'src_chs_lib',
                    'format' => 'raw',
                    'filter' => ['1' => '是', '2' => '否', '0' => '空'],
                    'headerOptions' => ['width' => '80'],
                    'value' => function ($model) {
                        $selected = !isset($model->src_chs_lib) ? 0 : $model->src_chs_lib;
                        $class = ['1' => 'yes', '2' => 'no', '0' => 'empty'][$selected];
                        return "<span class='{$class}'>" . ['1' => '是', '2' => '否', '0' => '空'][$selected] . '</span>';
                    },
                ],
                [
                    'attribute' => 'src_gb13000',
                    'format' => 'raw',
                    'filter' => ['1' => '是', '2' => '否', '0' => '空'],
                    'headerOptions' => ['width' => '80'],
                    'value' => function ($model) {
                        $selected = !isset($model->src_gb13000) ? 0 : $model->src_gb13000;
                        $class = ['1' => 'yes', '2' => 'no', '0' => 'empty'][$selected];
                        return "<span class='{$class}'>" . ['1' => '是', '2' => '否', '0' => '空'][$selected] . '</span>';
                    },
                ],
                [
                    'attribute' => 'src_old_lqhanzi',
                    'format' => 'raw',
                    'filter' => ['1' => '是', '2' => '否', '0' => '空'],
                    'headerOptions' => ['width' => '80'],
                    'value' => function ($model) {
                        $selected = !isset($model->src_old_lqhanzi) ? 0 : $model->src_old_lqhanzi;
                        $class = ['1' => 'yes', '2' => 'no', '0' => 'empty'][$selected];
                        return "<span class='{$class}'>" . ['1' => '是', '2' => '否', '0' => '空'][$selected] . '</span>';
                    },
                ],
                [
                    'attribute' => 'src_feijinchang',
                    'format' => 'raw',
                    'filter' => ['1' => '是', '2' => '否', '0' => '空'],
                    'headerOptions' => ['width' => '80'],
                    'value' => function ($model) {
                        $selected = !isset($model->src_feijinchang) ? 0 : $model->src_feijinchang;
                        $class = ['1' => 'yes', '2' => 'no', '0' => 'empty'][$selected];
                        return "<span class='{$class}'>" . ['1' => '是', '2' => '否', '0' => '空'][$selected] . '</span>';
                    },
                ],
//                [
//                    'attribute' => 'src_lqhanzi',
//                    'format' => 'raw',
//                    'filter' => ['1' => '是', '2' => '否', '0' => '空'],
//                    'headerOptions' => ['width' => '150'],
//                    'value' => function ($model) {
//                        $selected = !isset($model->src_lqhanzi) ? 0 : $model->src_lqhanzi;
//                        return Html::radioList('lqhz' . $model->id, $selected, ['1' => '是', '2' => '否', '0' => '空'], ['class' => 'lqhz', 'id' => $model->id]);
//                    },
//                ],
                // 'src_hujingyu',
                // 'lqhanzi_sn',
                // 'is_redundant',


                [
                    'attribute' => 'is_split_part',
                    'format' => 'raw',
                    'filter' => ['1' => '是', '2' => '否', '0' => '空'],
                    'headerOptions' => ['width' => '150'],
                    'value' => function ($model) {
                        $selected = !isset($model->is_split_part) ? 0 : $model->is_split_part;
                        return Html::radioList('stp' . $model->id, $selected, ['1' => '是', '2' => '否', '0' => '空'], ['class' => 'stp', 'id' => $model->id]);
                    },
                ],

//                [
//                    'attribute' => 'is_search_part',
//                    'format' => 'raw',
//                    'filter' => ['1' => '是', '2' => '否'],
//                    'headerOptions' => ['width' => '100'],
//                    'value' => function ($model) {
//                        $selected = !isset($model->is_search_part) ? 0 : $model->is_search_part;
//                        return Html::radioList('shp' . $model->id, $selected, ['1' => '是', '2' => '否'], ['class' => 'shp', 'id' => $model->id]);
//                    },
//                ],
//                [
//                    'attribute' => 'replace_parts',
//                    'format' => 'raw',
//                    'headerOptions' => ['width' => '120'],
//                    'value' => function ($model) {
//                        return Html::activeInput('text', $model, 'replace_parts', ['class' => 'form-control rp', 'id' => $model->id]);
//                    },
//                ],
                [
                    'attribute' => 'strokes',
                    'headerOptions' => ['width' => '80'],
                ],
                [
                    'attribute' => 'stroke_order',
                    'headerOptions' => ['width' => '80'],
                ],
                'remark',
                // 'c_t',
                // 'u_t',

                [
                    'headerOptions' => ['width' => '80'],
                    'class' => 'yii\grid\ActionColumn',
                ],
            ],
        ]); ?>
    </div>


<?php
$script = <<<SCRIPT
    $(document).on('click', '.lqhz', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var radioListName = 'lqhz' + id;
        var value = $('input[name="' + radioListName + '"]:checked').val();
        $.post( {
            url: "/hanzi-parts/check?field=lqhz&id=" + id,
            data: {value: value},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    obj.fadeOut("fast");
                    obj.fadeIn("fast");
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
    
    
    $(document).on('click', '.stp', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var radioListName = 'stp' + id;
        var value = $('input[name="' + radioListName + '"]:checked').val();
        $.post( {
            url: "/hanzi-parts/check?field=stp&id=" + id,
            data: {value: value},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    obj.fadeOut("fast");
                    obj.fadeIn("fast");
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
    
    
    $(document).on('click', '.shp', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var radioListName = 'shp' + id;
        var value = $('input[name="' + radioListName + '"]:checked').val();
        $.post( {
            url: "/hanzi-parts/check?field=shp&id=" + id,
            data: {value: value},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    obj.fadeOut("fast");
                    obj.fadeIn("fast");
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

    $(document).on('blur', '.rp', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var value = $(this).val();
        $.post( {
            url: "/hanzi-parts/check?field=rp&id=" + id,
            data: {value: value},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    obj.attr("disabled", "disabled");
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

    $(document).on('dblclick', '.rp', function() {
        $(this).removeAttr("disabled");
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
