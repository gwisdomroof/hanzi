<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\HanziSet;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GltwDedupResultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Gltw Dedup Results');
$this->params['breadcrumbs'][] = $this->title;
?>

    <style type="text/css">
        .hanzi {
            font-size: 35px;
        }

        .check {
            cursor: pointer;
        }

    </style>

    <div class="gltw-dedup-result-index">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'id',
                    'headerOptions' => ['width' => '80px'],
                ],
//            'source',
//            'type',
//            'word',
                [
                    'attribute' => 'pic_name',
                    'format' => 'image',
                    'value' => function ($model) {
                        return \common\models\HanziSet::getPicturePath($model->source, $model->pic_name);
                    },
                ],

                // 'nor_var_type',
                // 'belong_standard_word_code',
                // 'standard_word_code',
                // 'position_code',
                [
                    'attribute' => 'duplicate_id1',
                    'format' => 'html',
                    'value' => function ($model) {
                        if (mb_strlen($model->duplicate_id1, 'UTF-8') > 1) {
                            $imagePath = \common\models\HanziSet::getPicturePath(HanziSet::SOURCE_TAIWAN, $model->duplicate_id1);
                            return "<img src='{$imagePath}' alt='{$model->duplicate_id1}'><span class='dup-id'>{$model->duplicate_id1}</span>";
                        } else {
                            return "<div class='hanzi'>{$model->duplicate_id1}</div>";
                        }
                    },
                ],
                [
                    'attribute' => 'duplicate_id2',
                    'format' => 'html',
                    'value' => function ($model) {
                        if (mb_strlen($model->duplicate_id2, 'UTF-8') > 1) {
                            $imagePath = \common\models\HanziSet::getPicturePath(HanziSet::SOURCE_TAIWAN, $model->duplicate_id2);
                            return "<img src='{$imagePath}' alt='{$model->duplicate_id2}'><span class='dup-id'>{$model->duplicate_id2}</span>";
                        } else {
                            return "<div class='hanzi'>{$model->duplicate_id2}</div>";
                        }
                    },
                ],
                [
                    'attribute' => 'duplicate_id3',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $disable = empty($model->duplicate_id3) ? '' : 'disabled="disabled"';
                        return "<div><input type='text' class='form-control' {$disable} id='{$model->id}' name='{$model->id}' value='{$model->duplicate_id3}'/></div>";
                    },
                ],
                // 'duplicate_id3',
                // 'remark',
                // 'created_at',
                // 'updated_at',
                [
                    'header' => '操作',
                    'headerOptions' => ['width' => '120', 'style' => 'color:#337ab7'],
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{check} {update}',
                    'buttons' => [
                        'check' => function ($url, $model, $key) {
                            $options = [
                                'title' => $model->id,
                                'aria-label' => Yii::t('yii', '审查确认'),
                                'class' => 'check'
                            ];
                            return Html::a('<span>审查确认</span>&nbsp;', null, $options);
                        },
                        'update' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '更新'),
                                'aria-label' => Yii::t('yii', '更新'),
                                'target' => '_blank'
                            ];
                            $url = Url::toRoute(['gl-variant/update', 'id' => $key]);
                            return Html::a('<span>更新</span>', $url, $options);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>


<?php
$script = <<<SCRIPT
    $(document).on('click', '.check', function() {
        var id = $(this).attr('title');
        if ($('#'+id).is('[disabled=disabled]')) {
            $('#'+id).attr('disabled', false);
            return;
        }
        
        var value = $('#'+id).val();
        $.post( {
            url: "/gl-variant/check-save?id=" + id,
            data: {duplicate_id3: value},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
					$('#'+id).attr('disabled', true);
                    return true;
                }
            },
            error: function(result) {
                alert(result.msg)
            }
        });
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);