<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\HanziHyyt;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziHyytSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzi Hyyts');
// $this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .confirm, .modify {
        cursor: pointer;
        font-size: 14px;
    }
    .hanzi-image {
        width:50px;
    }
    #image-page {
        width: 100%;
    }
</style>

<div class="col-sm-6">
    <button class="btn btn-default glyphicon glyphicon-step-backward" id="page-backward" ></button>
    <button class="btn btn-default glyphicon glyphicon-zoom-in" onmousedown="changeSize('image-page','+');" onmouseup="window.clearTimeout(oTime);"></button>
    <button class="btn btn-default glyphicon glyphicon-zoom-out" onmousedown="changeSize('image-page','-');" onmouseup="window.clearTimeout(oTime);"></button>
    <button class="btn btn-default glyphicon glyphicon-step-forward" id="page-forward"></button>

    <div  style="float:left; overflow:scroll; height: 520px; border: 1px solid #eee;">
    <?php
    $imgName =  str_pad($curPage, 4, '0', STR_PAD_LEFT);
    echo Html::img("/img/hydzd/$imgName.png", ["id" => "image-page", "style" => "width: 100%;"]);
    ?>
    </div>
</div> 

<div class="col-sm-6" style="margin-top: 33px; overflow:scroll; height: 520px;">
    <table class="table table-hover">
        <tr style="background:#f9f9f9"><th width="15%">字头</th><th>类型</th><th width="15%">同某字</th><th>备注</th></tr>
        
        <?php foreach ($models as $model): ?>
            <form id=<?="form".$model->id?> >
            <tr><td>
            <?php if (!empty($model->word1)) {
               echo Html::activeInput('text', $model, 'word1', ['class' => 'form-control', 'id' => 'wd'.$model->id, 'readonly' => true]);
            } else {
                echo Html::img("/img/hy/$model->picture.png", ['class' => 'form-control hanzi-image', 'id' => 'wd'.$model->id, 'readonly' => true]); 
            }
            ?>
            </td><td>
            <?= Html::activeDropDownList ($model, 'type1', HanziHyyt::types(), ['prompt'=>'', 'class' => 'form-control', 'id' => 'tp'.$model->id, 'readonly' => true] ); ?>
            </td><td>
            <?= Html::activeInput('text', $model, 'tong_word1', ['class' => 'form-control', 'id' => 'tw'.$model->id, 'readonly' => true]); ?>
            </td><td>
            <?= Html::activeInput('text', $model, 'zhushi1', ['class' => 'form-control', 'id' => 'zs'.$model->id, 'readonly' => true]); ?>
            </td></tr>
            </form>
        <?php endforeach;?>
    </table>

    <ul class="pagination">
    <?php 
    $count = 10;
    $maxPage = 5127;
    $minPage = $curPage-(int)($count/2) > 1 ? $curPage-(int)($count/2) : 1;
    $maxPage = $minPage + $count < $maxPage ? $minPage + $count : $maxPage;
    if ($curPage > 1) {
        $prePage = $curPage-1;
        echo "<li class='prev'><a href='/hanzi-hyyt?page=$prePage'>«</a></li>";
    }
    for ($i=$minPage; $i <= $maxPage; $i++) { 
        if ($i == $curPage) {
            echo "<li class='active'><a href='/hanzi-hyyt?page=$i'>$i</a></li>";
        } else {
            echo "<li><a href='/hanzi-hyyt?page=$i'>$i</a></li>";
        }
    } 
    if ($curPage < $maxPage) {
        $nextPage = $curPage+1;
        echo "<li class='next'><a href='/hanzi-hyyt?page=$nextPage'>»</a></li>";
    }
    ?>
    </ul>

</div>

<?php
# 图片编号与urlPage编号一致
$curPage = (int)$curPage;

$script = <<<SCRIPT
    var oTime;
    function changeSize(id,action){
        var obj=document.getElementById(id);
        obj.style.width=parseInt(obj.style.width)+(action=='+'?+10:-10)+'%';
        oTime=window.setTimeout('changeSize(\''+id+'\',\''+action+'\')',100);
    }
    document.onmouseup=function(){
        window.clearTimeout(oTime);
    }

    var curPage = $curPage;
    $(document).on('click', '#page-backward', function() {
        var lastPage = parseInt(curPage) > 1 ? parseInt(curPage) - 1 : 1;
        lastPage = lastPage + "";
        var pad = "0000";
        var last = pad.substring(0, pad.length - lastPage.length) + lastPage;
        document.getElementById('image-page').src = "/img/hydzd/" + last +".png?r=" + Math.random();
        curPage = lastPage;
    });

    $(document).on('click', '#page-forward', function() {
        var nextPage = parseInt(curPage) + 1 < $maxPage ? parseInt(curPage) + 1 : $maxPage;
        nextPage = nextPage + "";
        var pad = "0000";
        var next = pad.substring(0, pad.length - nextPage.length) + nextPage;
        document.getElementById('image-page').src = "/img/hydzd/" + next +".png?r=" + Math.random();
        curPage = nextPage;
    });

    $(document).on('click', '.confirm', function() {
        var id = $(this).attr('name');
        $.post( {
            url: "/hanzi-hyyt/modify?id=" + id,
            data: $('#form'+id).serialize(),
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    $('#wd'+id).attr('disabled','disabled');
                    $('#tp'+id).attr('disabled','disabled');
                    $('#tw'+id).attr('disabled','disabled');
                    $('#zs'+id).attr('disabled','disabled');
                }
            },
            error: function(result) {
                alert(result.msg)
            }
        });
    });

    $(document).on('click', '.modify', function() {
        var id = $(this).attr('name');
        $('#wd'+id).removeAttr("disabled");
        $('#tp'+id).removeAttr("disabled");
        $('#tw'+id).removeAttr("disabled");
        $('#zs'+id).removeAttr("disabled");
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
