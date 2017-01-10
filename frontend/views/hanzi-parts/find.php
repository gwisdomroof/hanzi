<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\HanziSet;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CommonPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '部件集 / 查找');
$this->params['breadcrumbs'][] = $this->title;
?>

    <style>
        .result {
            margin-top: 20px;
        }

        .hanzi-item {
            border: 1px solid #eef;
            margin: 2px;
            cursor: pointer;
            width: 45px;
            height: 45px;
            font-size: 35px;
        }

        .hanzi-img {
            width: 45px;
            height: 45px;
        }

        .empty {
            background-color: yellow;
        }

        .choose {
            background-color: #99FF33;
            opacity: 0.8;
        }

        .cancel {
            background-color: #FF5511;
            opacity: 0.8;
        }

        .msg {
            font-size: 12px;
            font-style: italic;
            color: #888888;
            margin-left: 20px;
        }

        .reset-backend {
            float: right;
        }

    </style>

    <div class="hanzi-parts-index">

        <div class="col-sm-6">
            <form class="form-horizontal" action="/hanzi-parts/find" method="get">
                <label class="control-label" style="float: left" for="find-str">查　找：</label>
                <div class="col-sm-6">
                    <input type="text" id="find-str" class="form-control" name="find" value="<?= $find ?>">
                </div>
                <input type="hidden" id="page" class="form-control" name="page" value="<?= $page ?>">
                <input type="hidden" id="size" class="form-control" name="size" value="<?= $size ?>">
                <button type="submit" class="btn btn-primary">查找</button>

            </form>
        </div>
        <div class="clearfix" style="margin-top: 10px; margin-bottom: 10px"></div>
        <div class="col-sm-6">
            <div class="form-horizontal">
                <input type="hidden" id="repalce-id-set" class="form-control" name="repalce-id-set" value="">
                <label class="control-label" style="float: left" for="replace-str">替换为：</label>
                <div class="col-sm-6">
                    <input type="text" id="replace-str" class="form-control" name="replace" value="">
                </div>
                <button class="btn btn-primary" id="replace-btn">替换</button>
                <a class="btn  btn-default reset-backend" href="/hanzi-parts/clear-flag">重置后台标记</a>

            </div>
        </div>
        <div class="clearfix" style="margin-top: 10px; margin-bottom: 10px"></div>
        <div class="msg">
            <?php if (!empty($models)) {
                $count = count($models);
                echo "检索到{$count}条数据.";
            } ?>
        </div>

        <div class="col-sm-12 result">
            <?php
            foreach ($models as $variant) {
                $title = "{$variant->id}";
                $title .= "&#xa;初次：{$variant->initial_split11}|{$variant->initial_split12}|{$variant->deform_split10};";
                $title .= "&#xa;回查：{$variant->initial_split21}|{$variant->initial_split22}|{$variant->deform_split20};";

                if (!empty($variant->word)) {
                    $title = empty($variant->initial_split11) ? '' : HanziSet::norVarTypes()[$variant->nor_var_type];
                    echo "<span class='hanzi-item glyph' title='{$title}'  id='{$variant->id}'>{$variant->word}</span>";
                } elseif (!empty($variant->picture)) {
                    $picPath = HanziSet::getPicturePath($variant->source, $variant->picture);
                    echo "<span class='hanzi-item picture' title='{$title}' id='{$variant->id}'><img alt= '$variant->picture' src='$picPath' class='hanzi-img'></span>";
                } else {
                    echo "<span class='hanzi-item empty' title='{$title}'  id='{$variant->id}'>空</span>";
                }
            }
            ?>
        </div>
        <?php if (!empty($models)): ?>
            <div class="col-sm-12" style="margin-top: 20px;">
                <button type="button" class="btn btn-primary" id="choose-all">全选</button>
                <button type="button" class="btn btn-primary" id="cancel-all">反选</button>
                <button type="button" class="btn btn-primary" id="reset-all">重置</button>
            </div>
        <?php endif; ?>
    </div>


<?php
$script = <<<SCRIPT
    $(document).on('click', '.hanzi-item', function() {
        if($(this).hasClass('choose')) {
            $(this).removeClass('choose');
            $(this).addClass('cancel');
        } else {
            $(this).removeClass('cancel');
            $(this).addClass('choose');
        }
    });
    
    $(document).on('click', '#choose-all', function() {
        $('.hanzi-item').addClass('choose');
        $('.hanzi-item').removeClass('cancel');
    });
    
    $(document).on('click', '#cancel-all', function() {
        $('.hanzi-item').addClass('cancel');
        $('.hanzi-item').removeClass('choose');
    });
    
    $(document).on('click', '#reset-all', function() {
        $('.hanzi-item').removeClass('choose');
        $('.hanzi-item').removeClass('cancel');
    });
    
    $(document).on('click', '#replace-btn', function() {
        var idSet = '';
        $(".choose").each(function(){
            idSet = idSet + $(this).attr('id') + ',';
        });
        
        var noNeedReplaceIdSet = '';
        $(".cancel").each(function(){
            noNeedReplaceIdSet = noNeedReplaceIdSet + $(this).attr('id') + ',';
        });
        
        var find = $('#find-str').val();
        var replace = $('#replace-str').val();
        $.post( {
            url: "/hanzi-parts/replace?find=" + find + "&replace=" + replace,
            data: {idSet: idSet, noNeedReplaceIdSet: noNeedReplaceIdSet},
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    alert('替换'+result.replaceCount+'条记录，'+'取消'+result.noNeedReplaceCount+'条记录。');
                    return true;
                } else {
                    alert(result.msg);
                    return;
                }
            },
            error: function(result) {
                alert(result.msg)
            }
        });
        
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
