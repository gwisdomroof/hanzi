<?php

use yii\helpers\Html;
?>

<style type="text/css">
    .image-page {
        width: 80%;
    }
</style>

<div>
	<div class="button-group" style="position:absolute; float:left;">
	    <button class="btn btn-default glyphicon glyphicon-step-backward" id="page-backward" ></button>
	    <button class="btn btn-default glyphicon glyphicon-zoom-in" onmousedown="changeSize('image-page','+');" onmouseup="window.clearTimeout(oTime);"></button>
	    <button class="btn btn-default glyphicon glyphicon-zoom-out" onmousedown="changeSize('image-page','-');" onmouseup="window.clearTimeout(oTime);"></button>
	    <button class="btn btn-default glyphicon glyphicon-step-forward" id="page-forward" ></button>
	</div>

    <div  style="text-align: center; ">
    	<?php echo Html::img($url, ['class' => 'image-page', 'id' => 'image-page', "style" => "width: 75%"]); ?>
    </div>
</div>



<?php
# 图片编号与urlPage编号一致
preg_match("/\/([0-9]{3,5}).png$/", $url, $matches);
$curPage = isset($matches[1]) ? (int)$matches[1] : 1;


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
        var pad = "000";
        var last = pad.substring(0, pad.length - lastPage.length) + lastPage;
        document.getElementById('image-page').src = "/img/dhszd/" + last +".png?r=" + Math.random();
        curPage = lastPage;
    });

    $(document).on('click', '#page-forward', function() {
        var nextPage = parseInt(curPage) + 1 < $maxPage ? parseInt(curPage) + 1 : $maxPage;
        nextPage = nextPage + "";
        var pad = "000";
        var next = pad.substring(0, pad.length - nextPage.length) + nextPage;
        document.getElementById('image-page').src = "/img/dhszd/" + next +".png?r=" + Math.random();
        curPage = nextPage;
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);