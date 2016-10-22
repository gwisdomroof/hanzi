<?php
use common\models\HanziSet;
use common\models\GltwDedup;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Gltw Dedups')];
if (!empty($seq) && $seq == 2) {
    $this->params['breadcrumbs'][] = Yii::t('frontend', 'Second Stage');
} elseif (!empty($seq) && $seq == 3) {
    $this->params['breadcrumbs'][] = Yii::t('frontend', 'Third Stage');
}

?>

    <style type="text/css">
        .dic-title {
            color: #1a0dab;
            font-family: 宋体;
            font-size: 18px;
            margin-top: 20px;
        }

        .hanzi-normal {
            font-family: 楷体;
            font-size: 44px;
            width: 46px;
            color: #1a0dab;
            text-align: center;
        }

        .hanzi-normal a {
            color: #1a0dab;
        }

        .hanzi-item {
            font-size: 44px;
            margin: 0px 2px;
            color: black;
        }

        .hanzi-item a {
            color: #000;
        }

        .hanzi-img {
            margin: -2px 2px;
            padding: 0px 0px;
            width: 44px;
            height: 45px;
            vertical-align: text-top;
            cursor: pointer;
        }

        .param img {
            border: 1px solid red;
        }

        .gl-normal, .gl-variant, #gl-code {
            font-family: "Tripitaka UniCode";
            cursor: pointer;
        }

        #tw-variant {
            font-family: MingLiU;
        }

        .duplicate {
            background-color: aqua;
        }

        .duplicate-different {
            background-color: aqua;
            border: 1px solid red;
        }

        #tw-variants {
            border-right: 2px solid #eef;
            overflow-y: auto;
            max-height: 500px;
        }

        .tips {
            color: red;
        }
    </style>

    <div class="msg pull-right">
        <span id="tips" class="tips" style="display:none; margin-right:5px;">+2</span>
        当前积分：<span id="score"><?= \common\models\HanziUserTask::getScore(Yii::$app->user->id) ?></span>
        <?php if (!empty(Yii::$app->session->get('curDedupProgress'))) {
            list($finished, $schedule) = explode('/', Yii::$app->session->get('curDedupProgress'));
            echo "&nbsp;&nbsp;/&nbsp;&nbsp;日进度：<span id='finished'>{$finished}</span>/<span id='schedule'>$schedule</span>";
        } ?>
    </div>

<?php if (!empty($twData)) : ?>
    <div id='tw-variants' class="col-sm-6">
        <div class="dic-title">台灣異體字字典</div>
        <?php foreach ($twData as $normal => $variants) {
            echo "<div class='hanzi-normal'><a target='_blank' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $normal]) . "'>【<span>" . $normal . "</span>】</a></div>";
            echo "<div class='hanzi-variants'>";
            foreach ($variants as $variant) {
                if (!empty($variant->word)) {
                    $title = empty($variant->nor_var_type) ? $variant->word : $variant->word . '|' . HanziSet::norVarTypes()[$variant->nor_var_type];
                    if (!empty($variant->nor_var_type) && $variant->nor_var_type >= HanziSet::TYPE_NORMAL_WIDE) {
                        $title = "$title|{$variant->belong_standard_word_code}";
                    }
                    echo "<span class='hanzi-item' ><a target='_blank' class='variant' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $variant->word]) . "'>" . $variant->word . "</a></span>";
                } elseif (!empty($variant->pic_name)) {
                    $picPath = \common\models\HanziSet::getPicturePath($variant->source, $variant->pic_name);
                    $title = $variant->pic_name;
                    if (!empty($variant->nor_var_type)) {
                        $title = $title . '|' . HanziSet::norVarTypes()[$variant->nor_var_type];
                    }
                    if (!empty($variant->nor_var_type) && $variant->nor_var_type >= HanziSet::TYPE_NORMAL_WIDE) {
                        $title = "$title|{$variant->belong_standard_word_code}";
                    }
                    echo "<span class='hanzi-item' ><a target='_blank' class='variant' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $variant->pic_name]) . "'>" . "<img alt= '$variant->pic_name' src='$picPath' class='hanzi-img'></a></span>" . $variant->pic_name;
                }
            }
            echo "</div><br/>";
        } ?>
    </div>
<?php endif; ?>

<?php if (!empty($glData)) : ?>
    <div id='gl-variants' class="col-sm-6">
        <div class="dic-title" style="margin-top: 0px;">
            <span>高麗異體字字典</span>
            <?php $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'action' => ['next', 'id' => Yii::$app->request->get('id')],
                'id' => 'hanzi-form'
            ]); ?>
            <div class="form-group pull-right" style="margin-top: -20px;">
                <?php echo Html::submitButton(Yii::t('frontend', 'Completed And Next'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'next-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <?php foreach ($glData as $normal => $variants) {
            $class = 'param';
            $title = $normal == $model->unicode ? $normal : "{$normal}|{$model->unicode}";
            echo "<div class='hanzi-normal gl-normal' title='$title'>【{$normal}】</div>";
            echo "<div class='hanzi-variants'>";
            foreach ($variants as $variant) {
                if (empty($variant))
                    continue;
                $value = !empty($variant->word) ? $variant->word : $variant->pic_name;
                $picPath = \common\models\HanziSet::getPicturePath($variant->source, $value);
                $duplicateId = $variant->duplicate_id1 . ',' . $variant->duplicate_id2;
                $duplicateId = preg_replace('/(^,)|(,$)/', '', $duplicateId);
                $title = empty($duplicateId) ? $value : $value . '|' . $duplicateId;
                $class = 'hanzi-img';
                if (!empty($duplicateId)) {
                    if (!empty($variant->duplicate_id1) && !empty($variant->duplicate_id2)
                        && $variant->duplicate_id1 != $variant->duplicate_id2
                    ) {
                        $class = 'hanzi-img duplicate-different';
                    } else {
                        $class = 'hanzi-img duplicate';
                    }
                }
                echo "<div class='hanzi-item'><img alt='$value' title='$title' src='$picPath' class='$class'></div>";

            }
            echo "</div><br/>";
        } ?>
    </div>
<?php endif; ?>

    <div class="modal fade" id="mymodal">
        <div class="modal-dialog">
            <form action="gltw-dedup/save" id="gl-dedup">
                <div class="modal-content">
                    <div class="modal-body" style="min-height: 120px; margin-top: 10px;">
                        <label class="col-sm-3" for="tw-code"
                               style="text-align: right; margin-top: 5px; float:left;">高丽异体字</label>
                        <div class="col-sm-9" style="margin-bottom: 15px;">
                            <input type="input" id="gl-code" class="form-control" name="glCode" style="display:none;"/>
                            <img id="gl-img" alt="" title="" src="" class="hanzi-img">
                        </div>
                        <label class="col-sm-3" for="tw-code"
                               style="text-align: right; margin-top: 5px; float:left;">重复编号</label>
                        <div class="col-sm-9">
                            <input type="text" id="tw-code" class="form-control" name="twCode"
                                   placeholder="请输入与图片字重复的台湾异体字编号…"/>
                        </div>
                        <input type="hidden" name="seq" value="<?= $seq ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" id="commit">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php
$script = <<<SCRIPT
    var seq = $seq;
    var permission = $permission;
    $(document).on('click', '#gl-variants .hanzi-img', function() {
        if (permission == 0)
            return false;
        var title = $(this).attr('title');
        var glCode = title.split("|")[0];
        $('#gl-code').val(glCode);
        $('#gl-img').attr('title', glCode);
        $('#gl-img').attr('alt', glCode);
        $('#gl-img').attr('src', $(this).attr('src'));
        $('#gl-img').attr('class', $(this).attr('class'));
        var twCode = '';
        if (title.split("|").length > 1)
            twCode = title.split("|")[1];
        $('#tw-code').val(twCode);
        $('#mymodal').modal('toggle');
    });
    
    $(document).on('click', '#commit', function() {  
        var glCode = $('#gl-code').val();
        var twCode = $('#tw-code').val();
        $.post( {
            url: "/gltw-dedup/save",
            data: $('#gl-dedup').serialize(),
            dataType: 'json',
            success: function(result){
                if (result.status == 'success') {
                    if (twCode != '') {
                        $('img[alt="'+glCode+'"]').attr('class', 'hanzi-img duplicate');
                        $('img[alt="'+glCode+'"]').attr('title', glCode + '|' + twCode);
                    } else {
                        $('img[alt="'+glCode+'"]').attr('class', 'hanzi-img');
                        $('img[alt="'+glCode+'"]').attr('title', glCode);
                    }
                    var score = parseInt(result.score);
                    if (score != 0) {
                        $("#tips").fadeIn(50).fadeOut(500); 
                        var scoreValue = parseInt($('#score').text()) + score;
                        $('#score').text(scoreValue);
                    }
                    
                } else if(result.status == 'error') {
                    alert(result.msg);
                }
                $('#mymodal').modal('toggle');
                return true;
            },
            error: function(result) {
                $('#mymodal').modal('toggle');
                return false;
            }
        });
    });

SCRIPT;
$this->registerJs($script, \yii\web\View::POS_END);
