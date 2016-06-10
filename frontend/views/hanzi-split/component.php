<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;

require_once(__DIR__ . '/_components.php');


$this->title = Yii::t('frontend', '部件集', [
    'modelClass' => 'Hanzi',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .index {
        display: inline-block;
        width: 2em;
        position: absolute;
        margin: 5px;
    }
    .component-item {
        width: 30px;
        height: 30px;
        font-size: 24px;
        display: inline-table;
        border: 1px solid #eee;
        vertical-align: middle;
        margin: 0;
        padding: 0px;
        border-collapse: collapse;;
    }
    .component-img {
        width: 30px;
        height: 30px;
        font-size: 20px;
        display: inline-table;
        border: 1px solid #eee;
        vertical-align: middle;
        margin: 0;
        padding: 1px 3px 5px 3px;
        border-collapse: collapse;;
    }
</style>
<div class="hanzi-split-index">
	<div class="col-sm-offset-1 col-sm-10">
		<div style="margin-bottom:10px;">
		    <div class="col-sm-8">
		        <input type="text" id="ids" class="form-control col-sm-6" placeholder="请点选部件...">
		    </div>
		    <button type="button" id="ids-clear" class="btn btn-primary">清空</button>
	    </div>

        <table class="table">
            <?php foreach ($components as $stock_num => $stock_array): ?>
                <tr>
                    <td><span><?=$stock_num?></span></td>
                    <td>
                    <div>
                    <?php
                    $i = 1;
                    foreach ($stock_array as $key => $value) {
                        if(mb_strlen($key,'utf-8') == 1) {
                            echo "<span class='component-item' value='" . $value . "'>";
                            echo $key;
                        } else {
                            echo "<span>";
                            $path =  '/img/components/' . $key . '.png';
                            echo "<img class='component-img' src='$path' alt='$value'>";
                        }
                        echo "</span>";
                    } ?>
                    </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>



 <?php
$script = <<<SCRIPT
$('.component-item').click(function() {
    var value = $('#ids').val() + $(this).text();
    $('#ids').val(value);
});
$('.component-img').click(function() {
    var value = $('#ids').val() + $(this).attr("alt");
    $('#ids').val(value);
});
$('#ids-clear').click(function() {
    $('#ids').val('');
});
SCRIPT;
$this->registerJs($script, \yii\web\View::POS_END);

