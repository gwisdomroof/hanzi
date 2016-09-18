<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\HanziHyyt;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\HanziHyytSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzi Hyyts');
// $this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .container {
        width: 100%;
    }
    .confirm, .modify {
        cursor: pointer;
        font-size: 14px;
    }
    .hanzi-image {
        width:20px;
    }
    .tips {
        color: red;
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

<div class="msg pull-right">
    <span id="tips" class="tips" style="display:none; margin-right:5px;">+1</span>当前积分：<span id="score"><?=\common\models\HanziUserTask::getScore(Yii::$app->user->id)?></span>
</div>
<div class="col-sm-6" style="margin-top: 13px; overflow:scroll; height: 520px;">
    <table class="table table-hover" style="font-size: 16px;">
        <tr style="background:#f9f9f9; font-size:14px;"><th width="10%">序号</th><th width="10%">阶段</th><th width="15%">字头</th><th>类型</th><th width="15%">通行字</th><th>备注</th>
        <?php if ($writeable) {
            echo "<th width='15%'>操作</th>";
        } ?>
        </tr>
        
        <?php foreach ($models as $model): ?>
            <form id=<?="form".$model->id?> >
            <?php $bNew = $model->isNew($seq); ?>
            <!-- 初次阶段 -->
            </td><td style="border-bottom:hidden;">
            <?='';?>
            </td><td>
            <?='初次';?>
            </td><td>
            <?php if (!empty($model->word1)) {
               echo $model->word1;
            } else {
                echo Html::img("/img/hanzi/hy/$model->picture.png", ['class' => 'hanzi-image', 'id' => 'wd'.$model->id, 'disabled' => true]); 
            }
            ?>
            </td><td>
            <?= empty($model->type1) ? '' : HanziHyyt::types()[$model->type1]; ?>
            </td><td>
            <?= $model->tong_word1; ?>
            </td><td>
            <?= $model->zhushi1; ?>
            </td><td style="border-bottom:hidden;">
            <?='';?>
            </td></tr>

            <!-- 再次阶段 -->
            </td><td style="border-bottom:hidden;">
            <?='';?>
            </td><td>
            <?='再次';?>
            </td><td>
            <?php if (!empty($model->word2)) {
               echo $model->word2;
            } else {
                echo Html::img("/img/hanzi/hy/$model->picture.png", ['class' => 'hanzi-image', 'id' => 'wd'.$model->id, 'disabled' => true]); 
            }
            ?>
            </td><td>
            <?= empty($model->type2) ? '' : HanziHyyt::types()[$model->type2]; ?>
            </td><td>
            <?= $model->tong_word2; ?>
            </td><td>
            <?= $model->zhushi2; ?>
            </td><td style="border-bottom:hidden;">
            <?='';?>
            </td></tr>

            <!-- 审查阶段 -->
            </td><td> 
            <?= Html::activeInput('text', $model, 'num', ['class' => 'form-control', 'id' => 'xh'.$model->id, 'disabled' => !$bNew||!$writeable]); ?>
            </td><td>         
            <?='审查';?>
            </td><td>
            <?php if (!empty($model->word3)) {
               echo Html::activeInput('text', $model, 'word3', ['class' => 'form-control', 'id' => 'wd3'.$model->id, 'disabled' => !$bNew||!$writeable]);
            } else {
                echo Html::img("/img/hanzi/hy/$model->picture.png", ['class' => 'hanzi-image', 'id' => 'wd'.$model->id, 'disabled' => !$bNew||!$writeable]); 
            }
            ?>
            </td><td>
            <?= Html::activeDropDownList ($model, 'type3', HanziHyyt::types(), ['prompt'=>'', 'class' => 'form-control', 'id' => 'tp3'.$model->id, 'disabled' => !$bNew||!$writeable] ); ?>
            </td><td>
            <?= Html::activeInput('text', $model, 'tong_word3', ['class' => 'form-control', 'id' => 'tw3'.$model->id, 'disabled' => !$bNew||!$writeable]); ?>
            </td><td>
            <?= Html::activeInput('text', $model, 'zhushi3', ['class' => 'form-control', 'id' => 'zs3'.$model->id, 'disabled' => !$bNew||!$writeable]); ?>
            </td>
            <?php 
                if ($writeable) {
                   if($bNew) { 
                        echo "<td style='border-top:hidden;'><a class='confirm' name='" . $model->id . "' >确定</a>";
                    } else {
                        echo "<td style='border-top:hidden;'><a class='modify' name='" . $model->id . "' >修改</a>";
                    }
                    echo "<div class='clearfix'></div></td>";
                }
            ?>
            </tr>
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
        echo "<li class='prev'><a href='/hanzi-hyyt?page=$prePage&seq=$seq'>«</a></li>";
    }
    for ($i=$minPage; $i <= $maxPage; $i++) { 
        if ($i == $curPage) {
            echo "<li class='active'><a href='/hanzi-hyyt?page=$i&seq=$seq'>$i</a></li>";
        } else {
            echo "<li><a href='/hanzi-hyyt?page=$i&seq=$seq'>$i</a></li>";
        }
    } 
    if ($curPage < $maxPage) {
        $nextPage = $curPage+1;
        echo "<li class='next'><a href='/hanzi-hyyt?page=$nextPage&seq=$seq'>»</a></li>";
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
        var thisObj = $(this);
        var seq = $seq;
        $.post( {
            url: "/hanzi-hyyt/modify?id=" + id + "&seq=" + seq,
            data: $('#form'+id).serialize(),
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    $('#xh'+id).attr('disabled','disabled');
                    $('#wd3'+id).attr('disabled','disabled');
                    $('#tp3'+id).attr('disabled','disabled');
                    $('#tw3'+id).attr('disabled','disabled');
                    $('#zs3'+id).attr('disabled','disabled');
                    thisObj.attr('class', 'modify');
                    thisObj.text('修改');
                    var score = parseInt(result.score);
                    if (score != 0) {
                        var value = parseInt($('#score').text()) + score;
                        $("#tips").fadeIn(50).fadeOut(500); 
                        $('#score').text(value);
                    }
                    return true;
                }
            },
            error: function(result) {
                alert(result.msg)
            }
        });
    });

    $(document).on('click', '.modify', function() {
        var id = $(this).attr('name');
        $('#xh'+id).removeAttr("disabled");
        $('#wd3'+id).removeAttr("disabled");
        $('#tp3'+id).removeAttr("disabled");
        $('#tw3'+id).removeAttr("disabled");
        $('#zs3'+id).removeAttr("disabled");
        $(this).attr('class', 'confirm');
        $(this).text('确定');
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
