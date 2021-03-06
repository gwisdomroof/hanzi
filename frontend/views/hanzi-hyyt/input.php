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
            width: 50px;
        }

        .msg {
            color: #777;
        }

        .tips {
            color: red;
        }

        #image-page {
            width: 100%;
        }
    </style>

    <script>
        document.body.onload = function () {
            var height = document.body.clientHeight - 110;
            $('#left-pic').height(height);
            $('#right-check').height(height);
        };
    </script>


    <div class="col-sm-6" id="left-pic">
        <button class="btn btn-default glyphicon glyphicon-step-backward" id="page-backward"></button>
        <button class="btn btn-default glyphicon glyphicon-zoom-in" onmousedown="changeSize('image-page','+');"
                onmouseup="window.clearTimeout(oTime);"></button>
        <button class="btn btn-default glyphicon glyphicon-zoom-out" onmousedown="changeSize('image-page','-');"
                onmouseup="window.clearTimeout(oTime);"></button>
        <button class="btn btn-default glyphicon glyphicon-step-forward" id="page-forward"></button>

        <div style="float:left; overflow:scroll; height: 100%; border: 1px solid #eee;">
            <?php
            $imgName = str_pad($curPage, 4, '0', STR_PAD_LEFT);
            echo Html::img("/img/hydzd/$imgName.png", ["id" => "image-page", "style" => "width: 100%;"]);
            ?>
        </div>
    </div>

    <div class="msg pull-right">
        <span id="tips" class="tips" style="display:none; margin-right:5px;">+1</span>
        当前积分：<span id="score"><?= \common\models\HanziUserTask::getScore(Yii::$app->user->id) ?></span>
        <?php if (!empty(Yii::$app->session->get('curRecognizeProgress'))) {
            list($finished, $schedule) = explode('/', Yii::$app->session->get('curRecognizeProgress'));
            echo "&nbsp;&nbsp;/&nbsp;&nbsp;日进度：<span id='finished'>{$finished}</span>/<span id='schedule'>$schedule</span>";
        } ?>
    </div>

    <div class="col-sm-6" id="right-check" style="margin-top: 13px; overflow:scroll; height: 520px;">
        <table class="table table-hover">
            <tr style="background:#f9f9f9">
                <th width="15%">字头</th>
                <th>类型</th>
                <th width="15%">通行字</th>
                <th>备注</th>
                <?php if ($writeable) {
                    echo "<th width='15%'>操作</th>";
                } ?>
            </tr>

            <?php foreach ($models as $model): ?>
                <form id=<?= "form" . $model->id ?>>
                    <?php $bNew = $model->isNew($seq); ?>
                    <tr>
                        <td>
                            <?php if (!empty($model->word1)) {
                                echo Html::activeInput('text', $model, 'word' . $seq, ['class' => 'form-control', 'id' => 'wd' . $model->id, 'disabled' => !$bNew || !$writeable]);
                            } else {
                                echo Html::img("/img/hanzi/hy/$model->picture.png", ['class' => 'form-control hanzi-image', 'id' => 'wd' . $model->id, 'disabled' => !$bNew || !$writeable]);
                            }
                            ?>
                        </td>
                        <td>
                            <?= Html::activeDropDownList($model, 'type' . $seq, HanziHyyt::types(), ['prompt' => '', 'class' => 'form-control', 'id' => 'tp' . $model->id, 'disabled' => !$bNew || !$writeable]); ?>
                        </td>
                        <td>
                            <?= Html::activeInput('text', $model, 'tong_word' . $seq, ['class' => 'form-control', 'id' => 'tw' . $model->id, 'disabled' => !$bNew || !$writeable]); ?>
                        </td>
                        <td>
                            <?= Html::activeInput('text', $model, 'zhushi' . $seq, ['class' => 'form-control', 'id' => 'zs' . $model->id, 'disabled' => !$bNew || !$writeable]); ?>
                        </td>
                        <?php
                        # 页面权限和数据权限综合判断
                        if ($writeable) {
                            $dataAuthority = true;
                            if ($pageStatus == \common\models\HanziTask::STATUS_CONTINUE
                                && !empty($finished['mine'])
                                && !in_array($model->id, $finished['mine'])
                            ) {
                                $dataAuthority = false;
                            } elseif ($pageStatus != \common\models\HanziTask::STATUS_CONTINUE
                                && !empty($finished['others'])
                                && in_array($model->id, $finished['others'])
                            ) {
                                $dataAuthority = false;
                            }
                            if ($dataAuthority) {
                                if ($bNew) {
                                    echo "<td><a class='confirm' name='" . $model->id . "' >确定</a>";
                                } else {
                                    echo "<td><a class='modify' name='" . $model->id . "' >修改</a>";
                                }
                            }
                            echo "<div class='clearfix'></div></td>";
                        }
                        ?>
                    </tr>
                </form>
            <?php endforeach; ?>
        </table>

        <ul class="<?= $writeable ? 'pagination hidden' : 'pagination'; ?>">
            <?php
            $count = 10;
            $maxPage = 5127;
            $minPage = $curPage - (int)($count / 2) > 1 ? $curPage - (int)($count / 2) : 1;
            $maxPage = $minPage + $count - 1 < $maxPage ? $minPage + $count - 1 : $maxPage;
            if ($curPage > 1) {
                $prePage = $curPage - 1;
                echo "<li class='prev'><a href='/hanzi-hyyt?page=$prePage&seq=$seq'>«</a></li>";
            }
            for ($i = $minPage; $i <= $maxPage; $i++) {
                if ($i == $curPage) {
                    echo "<li class='active'><a href='/hanzi-hyyt?page=$i&seq=$seq'>$i</a></li>";
                } else {
                    echo "<li><a href='/hanzi-hyyt?page=$i&seq=$seq'>$i</a></li>";
                }
            }
            if ($curPage < $maxPage) {
                $nextPage = $curPage + 1;
                echo "<li class='next'><a href='/hanzi-hyyt?page=$nextPage&seq=$seq'>»</a></li>";
            }
            ?>
        </ul>

        <?php if ($writeable) { ?>
            <a href="/hanzi-hyyt/recognize" class="btn btn-primary">下一页</a>
        <?php } ?>
    </div>

<?php
# 图片编号与urlPage编号一致
$curPage = (int)$curPage;
$seq = (int)$seq;

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
        $.post( {
            url: "/hanzi-hyyt/modify?id=" + id + "&seq=" + $seq,
            data: $('#form'+id).serialize(),
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    $('#wd'+id).attr('disabled', true);
                    $('#tp'+id).attr('disabled', true);
                    $('#tw'+id).attr('disabled', true);
                    $('#zs'+id).attr('disabled', true);
                    var score = parseInt(result.score);
                    if (score != 0) {
                        $("#tips").fadeIn(50).fadeOut(500); 
                        var scoreValue = parseInt($('#score').text()) + score;
                        $('#score').text(scoreValue);
                        var finishedValue = parseInt($('#finished').text()) + 1;
                        $('#finished').text(finishedValue);
                    }
                    thisObj.attr('class', 'modify');
                    thisObj.text('修改');
                    return true;
                }
            },
            error: function(result) {
            }
        });
    });

    $(document).on('click', '.modify', function() {
        var id = $(this).attr('name');
        $('#wd'+id).attr('disabled', false);
        $('#tp'+id).attr('disabled', false);
        $('#tw'+id).attr('disabled', false);
        $('#zs'+id).attr('disabled', false);
        $(this).attr('class', 'confirm');
        $(this).text('确定');
    });

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
