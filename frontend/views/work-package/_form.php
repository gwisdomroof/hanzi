<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\WorkPackage */
/* @var $form yii\widgets\ActiveForm */
?>

    <style type="text/css">
        .schedule {
            float: left;
            margin-right: 10px;
        }
        .self-define {
            display: none;
        }
    </style>

    <div class="work-package-form col-sm-10">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal'
        ]); ?>
        <?= $form->field($model, 'type')->dropDownList(\common\models\WorkPackage::types(), ['disabled' => !$model->isNewRecord]) ?>
        <?= $form->field($model, 'volume')->dropDownList(['100' => 100, '200' => 200, '500' => 500, '1000' => 1000], ['prompt' => '', 'disabled' => !$model->isNewRecord]) ?>
        <div class="form-group field-workpackage-daily_schedule">
            <label class="control-label col-sm-3" for="workpackage-daily_schedule">每日计划</label>
            <div class="col-sm-2">
                <?= Html::activeDropDownList($model, 'daily_schedule', [
                    '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '0' => '自定义...'
                ], ['class' => 'schedule form-control']); ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'selfSchedule', ['template' => "{input}\n{hint}\n{error}"])->textInput(['class' => 'self-define form-control', 'placeholder' => '请输入5的倍数']); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="col-sm-2 pull-right">
        <nav class="navbar" style="background-color: #FFF8DC; border: 1px solid #E0DCBF;">
            <ul class="nav">
                <li><a href="<?= Url::toRoute(['index']) ?>">进行中</a></li>
                <li><a href="<?= Url::toRoute(['finished']) ?>">已完成</a></li>
                <li style="background-color:#f5f5f5;"><a href="<?= Url::toRoute(['create']) ?>">领任务</a></li>
                <li><a href="<?= Url::toRoute(['detail']) ?>">详　情</a></li>
            </ul>
        </nav>
    </div>
<?php
$script = <<<SCRIPT
    $(document).on('change', '.schedule', function() {
        var value = $(".schedule ").val();
        if (value == 0) {
            $('.self-define').css('display', 'block');
            $('.self-define').val('');
        } else {
            $('.self-define').css('display', 'none');
            $('.self-define').val(value);
        }
    });

SCRIPT;
$this->registerJs($script, \yii\web\View::POS_END);
