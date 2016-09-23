<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\LqVariant;


/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LqVariantCheckSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Lq Variant Checks'), 'url' => ['list']];
?>
    <style type="text/css">
        .confirm, .modify {
            cursor: pointer;
            font-size: 14px;
        }
        .search-input {
            cursor: pointer;
            font-size: 14px;
        }

        .hanzi-image {
            width: 40px;
        }

        .container {
            width: 100%;
        }
        .search-input {
            width: 30%;
            float: left;
        }
        .normal {
            color: #337ab7;
            cursor: pointer;
        }
        .search-input label {
            margin-top: 6px;
        }
    </style>

    <script>
        document.body.onload = function () {
            var height = document.body.clientHeight - 120;
            $('#variant-check').height(height);
            $('#variant-search').height(height);
        };
    </script>

    <div id='variant-check' class="lq-variant-check-index col-sm-8" style="overflow:scroll; height: 520px;">

        <div class="lq-variant-check-search">
            <?php $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'method' => 'post',
            ]); ?>
            <?php echo $form->field($searchModel, 'level', [
                'options' => ['class' => 'search-input',  'style'=>'width:30%;'],
                'labelOptions' => ['class' => 'col-sm-3'],
                'template' => '{label}<div class="col-sm-9">{input}</div>'
            ])->dropDownList(\common\models\LqVariantCheck::levels(), ['prompt' => '']) ?>
            <?php echo $form->field($searchModel, 'bconfirm', [
                'options' => ['class' => 'search-input',  'style'=>'width:50%;'],
                'labelOptions' => ['class' => 'col-sm-3'],
                'template' => '{label} <div class="col-sm-9">{input}</div>'
            ])->inline()->radioList([1 => '是', 0 => '否', 2 => '？']) ?>
            <?php echo Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>

        <table class="table table-hover">
            <tr style="background:#f9f9f9; color:#337ab7;">
                <th>&nbsp;</th>
                <th>图片名</th>
                <th>查字典</th>
                <th width="10%">正字</th>
                <th width="15%">异体字编号</th>
                <th>正异类型</th>
                <th>难易等级</th>
                <th>审核</th>
                <th>操作</th>
            </tr>

            <?php foreach ($dataProvider->getModels() as $model): ?>
                <form id=<?= "form" . $model->id ?>>
                    <?php $bNew = $model->isNew(); ?>
                    <tr>
                        <td>
                            <?php if (!empty($model->pic_name)) {
                                $normal = !empty($model->origin_standard_word_code) ? $model->origin_standard_word_code : $model->belong_standard_word_code;
//                                echo Html::img("/img/FontImage/" . $normal . "/$model->pic_name", ['class' => 'hanzi-image']);
                                $source = LqVariant::sources()[$model->source];
                                $username= $model['user']['username'];
                                $created_at = str_replace('"','',date("Y-m-dH:i:s",$model->created_at));
                                $title = "来源：{$model->source}&#xa;创建时间：{$created_at}&#xa;用户名：{$username}&#xa;备注：{$model->remark}";
//                                $title= "来源：{$source}&#xa;用户名：{$username}&#xa;备注：{$model->remark}&#xa;创建时间：{$created_at}";
                                echo "<a data-toogle='tooltip', title={$title}>".Html::img("/img/FontImage/{$normal}/{$model->pic_name}", ['class' => 'hanzi-image'])."</a>";
                            } ?>
                        </td>
                        <td>
                            <?php
                            $title = str_replace('.jpg', '', $model->pic_name);

                            $value = mb_strlen($title) > 7 ? mb_substr($title, 0, 7, 'UTF-8') . '...' : $title;
                            echo "<div title='$title'>$value</div>";
                            ?>
                        </td>
                        <td>
                            <?php echo "<div class='normal'>" . $model->belong_standard_word_code . "</div>";
                            ?>
                        </td>
                        <td>
                            <?= Html::activeInput('text', $model, 'belong_standard_word_code', ['class' => 'form-control', 'id' => 'sw' . $model->id, 'disabled' => !$bNew]); ?>
                        </td>
                        <td>
                            <?= Html::activeInput('text', $model, 'variant_code', ['class' => 'form-control', 'id' => 'vc' . $model->id, 'disabled' => !$bNew]); ?>
                        </td>
                        <td>
                            <?= Html::activeDropDownList($model, 'nor_var_type', \common\models\HanziSet::norVarTypes(), ['prompt' => '', 'class' => 'form-control', 'id' => 'nv' . $model->id, 'disabled' => !$bNew]); ?>
                        </td>
                        <td>
                            <?= Html::activeDropDownList($model, 'level', \common\models\LqVariantCheck::levels(), ['prompt' => '', 'class' => 'form-control', 'id' => 'lv' . $model->id, 'disabled' => !$bNew]); ?>
                        </td>
                        <td>
                            <?= Html::activeRadioList($model, 'bconfirm', [1 => '是', 0 => '否', 2 => '？'], ['prompt' => '', 'alt' => $model->id, 'class' => 'choose', 'id' => 'bc' . $model->id, 'disabled' => !$bNew]); ?>
                        </td>
                        <td>
                            <?php if ($bNew) {
                                echo "<a class='confirm' name='" . $model->id . "' >确定</a>";
                            } else {
                                echo "<a class='modify' name='" . $model->id . "' >修改</a>";
                            } ?>
                        </td>
                    <tr>

                </form>
            <?php endforeach; ?>
        </table>

        <ul class="pagination">
            <?php

            $count = 10;
            $curPage = (int)$dataProvider->pagination->page + 1;
            $maxPage = $dataProvider->pagination->pageCount;
            $minPage = $curPage - (int)($count / 2) > 1 ? $curPage - (int)($count / 2) : 1;
            $maxPage = $minPage + $count - 1 < $maxPage ? $minPage + $count - 1 : $maxPage;
            $url = Url::current();
            if (isset(Yii::$app->request->queryParams['page'])) {
                $url = str_replace("page={$curPage}&",  '' ,  $url);
                $url = str_replace("&page={$curPage}",  '' ,  $url);
            }
            if ($curPage > 1) {
                $prePage = $curPage - 1;
                echo "<li class='prev'><a href='$url&page=$prePage'>«</a></li>";
            }
            for ($i = $minPage; $i <= $maxPage; $i++) {
                if ($i == $curPage) {
                    echo "<li class='active'><a href='$url&page=$i'>$i</a></li>";
                } else {
                    echo "<li><a href='$url&page=$i'>$i</a></li>";
                }
            }
            if ($curPage < $maxPage) {
                $nextPage = $curPage + 1;
                echo "<li class='next'><a href='$url&page=$nextPage'>»</a></li>";
            }
            ?>
        </ul>

    </div>

    <div id='variant-search' class="lq-variant-search col-sm-4">
        <iframe id="search-result" style="border:none; width:100%; overflow:scroll; height: 520px;>"
                src="<?= Url::toRoute(['hanzi-dict/msearch']); ?>"></iframe>

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
                    $('#bc'+id).attr('disabled', true);
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
        $('#bc'+id).attr('disabled', false);
        $(this).attr('class', 'confirm');
        $(this).text('确定');
    });

    $(document).on('click', '.normal', function() {
        var url = '/hanzi-dict/msearch?param=' + $(this).text();
        $('#search-result').attr('src', url);

    });

    $(document).on('click', '.choose', function() {
        var id = $(this).attr('alt');
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
                    $('#bc'+id).attr('disabled', true);
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
