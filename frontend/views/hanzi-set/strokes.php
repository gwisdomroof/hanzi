<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\HanziSetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzi Sets');
$this->params['breadcrumbs'][] = $this->title;
?>

    <style>
        .hanzi-img {
            cursor: pointer;
        }
        .container {
            width: 95%;
        }
    </style>

    <div class="hanzi-set-index col-sm-5" style='height: 800px; overflow-y: auto'>

        <?php echo GridView::widget([
            'dataProvider' => $models,
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
            'columns' => [
                // ['class' => 'yii\grid\SerialColumn'],

                [
                    'header' => '', # 取消排序
                    'attribute' => 'id',
                    "headerOptions" => ["width" => "30"]
                ],
                [
                    'header' => '', # 取消排序
                    'attribute' => 'pic_name',
                    'format' => ['raw'],
                    'value' => function ($model) {
                        $picPath = \common\models\HanziSet::getPicturePath($model->source, $model->pic_name);
                        return "<img alt= '$model->pic_name' title='$model->pic_name' src='$picPath' class='hanzi-img'></a></span>";
                    },
                    "headerOptions" => ["width" => "50"],
                ],
                [
                    'header' => '', # 取消排序
                    'attribute' => 'belong_standard_word_code',
                    "headerOptions" => ["width" => "50"],
                ],
                [
                    'header' => '部首', # 取消排序
                    'attribute' => 'radical',
                    'format' => 'raw',
                    'headerOptions' => ['width' => '120'],
                    'value' => function ($model) {
                        $disabled = empty($model->radical) ? false : 'disabled';
                        return Html::activeInput('text', $model, 'radical', ['class' => 'form-control ra', 'id' => $model->id, 'disabled' => $disabled]);
                    },
                ],
                [
                    'header' => '笔画', # 取消排序
                    'attribute' => 'max_stroke',
                    'format' => 'raw',
                    'headerOptions' => ['width' => '120'],
                    'value' => function ($model) {
                        $disabled = empty($model->radical) ? false : 'disabled';
                        return Html::activeInput('text', $model, 'max_stroke', ['class' => 'form-control ms', 'id' => $model->id, 'disabled' => $disabled]);
                    },
                ],
            ],
        ]); ?>

    </div>

    <div class="col-sm-7">
        <iframe id='iframe' src='' style='width:100%; height: 800px;'></iframe>
    </div>


<?php
$script = <<<SCRIPT

    $(document).on('blur', '.ra', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var value = $(this).val();
        if (value == '') return;
        $.post( {
            url: "/hanzi-set/save?field=ra&id=" + id,
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
    
    $(document).on('dblclick', '.ra', function() {
        $(this).removeAttr("disabled");
    });
    
     $(document).on('blur', '.ms', function() {
        var obj = $(this);
        var id = $(this).attr('id');
        var value = $(this).val();
        if (value == '') return;
        $.post( {
            url: "/hanzi-set/save?field=ms&id=" + id,
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
    
    $(document).on('dblclick', '.ms', function() {
        $(this).removeAttr("disabled");
    });
    
    $(document).on('click', '.hanzi-img', function() {
        var url = "/hanzi-dict/taiwan?param=" + $(this).attr("title");
        $('#iframe').attr("src",url);  
    });
    

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
