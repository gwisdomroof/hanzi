<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\HanziSet;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziSetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '高丽藏去重');
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .confirm, .modify {
        cursor: pointer;
        font-size: 14px;
    }
</style>

<div class="col-sm-offset-2 col-sm-8 gaoli-dedup">
    <table class="table table-hover">
        <tr style="background:#f9f9f9"><th width="15%">异体字</th><th width="10%">编号</th><th width="15%">郑码</th><th>所属正字</th><th>重复值</th><th width="15%">操作</th></tr>
        
        <?php foreach ($models as $model): ?>
            <form id=<?="form".$model->id?> >
            <?php $bNew = empty($model->duplicate_id); ?>
            <tr><td>
            <?php $path = HanziSet::getPicturePath($model->source, $model->pic_name);
                echo Html::img($path, ['class' => 'hanzi-image', 'id' => 'yt'.$model->id, 'disabled' => !$bNew]);
            ?>
            </td><td>
			<?=$model->pic_name?>
            </td><td>
            <?= $model->zhengma; ?>
            </td><td style="font-family: Tripitaka UniCode; font-size: 20px;">
            <?= $model->belong_standard_word_code; ?>
            </td><td>
            <?= Html::activeInput('text', $model, 'duplicate_id', ['class' => 'form-control', 'id' => 'cf'.$model->id, 'disabled' => !$bNew]); ?>
            </td><td>
            <?php if ($bNew) { 
                    echo "<a class='confirm' name='" . $model->id . "' id='cm".$model->id."'>确定</a>";
                } else {
                    echo "<a class='modify' name='" . $model->id . "' id='cm".$model->id."' >修改</a>";
                }
                echo "<div class='clearfix'></div>";
            ?>
            </td></tr>
            </form>
        <?php endforeach;?>
    </table>

    <ul class="pagination">
    <?php 
    $count = 10;
    $maxPage = 435;
    $minPage = $curPage-(int)($count/2) > 1 ? $curPage-(int)($count/2) : 1;
    $maxPage = $minPage + $count -1 < $maxPage ? $minPage + $count -1 : $maxPage;
    if ($curPage > 1) {
        $prePage = $curPage-1;
        echo "<li class='prev'><a href='/de-dup/gaoli?page=$prePage'>«</a></li>";
    }
    for ($i=$minPage; $i <= $maxPage; $i++) { 
        if ($i == $curPage) {
            echo "<li class='active'><a href='/de-dup/gaoli?page=$i'>$i</a></li>";
        } else {
            echo "<li><a href='/de-dup/gaoli?page=$i'>$i</a></li>";
        }
    } 
    if ($curPage < $maxPage) {
        $nextPage = $curPage+1;
        echo "<li class='next'><a href='/de-dup/gaoli?page=$nextPage'>»</a></li>";
    }
    ?>
    </ul>

</div>


<?php
# 图片编号与urlPage编号一致
$curPage = (int)$curPage;

$script = <<<SCRIPT
    $(document).on('click', '.confirm', function() {  
        var id = $(this).attr('name');
        var thisObj = $(this);
        $.post( {
            url: "/de-dup/gl-save?id=" + id,
            data: $('#form'+id).serialize(),
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
					$('#cf'+id).attr('disabled', true);
                    thisObj.attr('class', 'modify');
                    thisObj.text('修改');
					
                	var dupId = result.dupId;
                	var dupValue = result.dupValue;
                	$('#cf'+dupId).val(dupValue);
					$('#cf'+dupId).attr('disabled', true);
                    $('#cm'+dupId).attr('class', 'modify');
                    $('#cm'+dupId).text('修改');
                    
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
        $('#cf'+id).attr('disabled', false);
        $(this).attr('class', 'confirm');
        $(this).text('确定');
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);