<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LqVariantCheckSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '异体字判定');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .confirm, .modify {
        cursor: pointer;
        font-size: 14px;
    }
    .hanzi-image {
        width:40px;
    }
    .container {
        width: 100%;
    }
    .normal {
        color:#337ab7;
        cursor:pointer;
    }
</style>

<div class="lq-variant-check-index col-sm-7" style="overflow:scroll; height: 520px;">

    <table class="table table-hover" >
        <tr style="background:#f9f9f9; color:#337ab7;"><th>异体字</th><th>查字典</th><th width="15%">正字</th><th width="15%">异体字编号</th><th>正异类型</th><th>难易等级</th><th>操作</th></tr>
        
         <?php foreach ($dataProvider->getModels() as $model): ?>
            <form id=<?="form".$model->id?> >
            <?php $bNew = $model->isNew(); ?>
            <tr><td>
            <?php if (!empty($model->pic_name)) {
                echo Html::img("/img/FontImage/".$model->belong_standard_word_code1."/$model->pic_name", ['class' => 'hanzi-image']);
            }?>
            </td><td>
            <?php echo "<div class='normal'>". $model->belong_standard_word_code2 . "</div>";
            ?>
            </td><td>
            <?= Html::activeInput('text', $model, 'belong_standard_word_code2', ['class' => 'form-control', 'id' => 'sw'.$model->id, 'disabled' => !$bNew]); ?>
            </td><td>
            <?= Html::activeInput('text', $model, 'variant_code2', ['class' => 'form-control', 'id' => 'vc'.$model->id, 'disabled' => !$bNew]); ?>
            </td><td>
            <?= Html::activeDropDownList ($model, 'nor_var_type2', \common\models\HanziSet::norVarTypes(), ['prompt'=>'', 'class' => 'form-control', 'id' => 'nv'.$model->id, 'disabled' => !$bNew] ); ?>
            </td><td>
            <?= Html::activeDropDownList ($model, 'level2', \common\models\LqVariantCheck::levels(), ['prompt'=>'', 'class' => 'form-control', 'id' => 'lv'.$model->id, 'disabled' => !$bNew] ); ?>
            </td><td>
            <?php if($bNew) { 
                echo "<a class='confirm' name='" . $model->id . "' >确定</a>";
            } else {
                echo "<a class='modify' name='" . $model->id . "' >修改</a>";
            }?>
            </td><tr>

            </form>
        <?php endforeach;?>
    </table>

    <ul class="pagination">
    <?php
    $count = 10;
    $curPage = (int)$dataProvider->pagination->page + 1;
    $maxPage = $dataProvider->pagination->pageCount;
    $minPage = $curPage-(int)($count/2) > 1 ? $curPage-(int)($count/2) : 1;
    $maxPage = $minPage + $count -1 < $maxPage ? $minPage + $count -1 : $maxPage;
    if ($curPage > 1) {
        $prePage = $curPage-1;
        echo "<li class='prev'><a href='/lq-variant-check/index?page=$prePage'>«</a></li>";
    }
    for ($i=$minPage; $i <= $maxPage; $i++) { 
        if ($i == $curPage) {
            echo "<li class='active'><a href='/lq-variant-check/index?page=$i'>$i</a></li>";
        } else {
            echo "<li><a href='/lq-variant-check/index?page=$i'>$i</a></li>";
        }
    } 
    if ($curPage < $maxPage) {
        $nextPage = $curPage+1;
        echo "<li class='next'><a href='/lq-variant-check/index?page=$nextPage'>»</a></li>";
    }
    ?>
    </ul>

</div>

<div class="lq-variant-search col-sm-5">
<iframe id="search-result" style="border:none; width:100%; overflow:scroll; height: 520px;>" src="<?=Url::toRoute(['hanzi-set/hsearch']);?>"></iframe>

</div>


<?php
$script = <<<SCRIPT
    var curPage = $curPage;
    $(document).on('click', '.confirm', function() {  
        var id = $(this).attr('name');
        var thisObj = $(this);
        $.post( {
            url: "/lq-variant-check/modify?id=" + id,
            data: $('#form'+id).serialize(),
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    $('#sw'+id).attr('disabled', true);
                    $('#vc'+id).attr('disabled', true);
                    $('#nv'+id).attr('disabled', true);
                    $('#lv'+id).attr('disabled', true);
                    thisObj.attr('class', 'modify');
                    thisObj.text('修改');
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
        $('#sw'+id).attr('disabled', false);
        $('#vc'+id).attr('disabled', false);
        $('#nv'+id).attr('disabled', false);
        $('#lv'+id).attr('disabled', false);
        $(this).attr('class', 'confirm');
        $(this).text('确定');
    });

    $(document).on('click', '.normal', function() {
        var url = '/hanzi-set/hsearch?HanziSetSearch[param]=' + $(this).text();
        $('#search-result').attr('src', url);

    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
