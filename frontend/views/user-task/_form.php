<?php

use yii\helpers\Html;
use common\models\HanziUserTask;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\HanziUserTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hanzi-user-task-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <!-- <?= $form->field($model, 'userid')->textInput() ?> -->

    <div class="form-group field-hanziusertask-userid">
        <label for="hanziusertask-userid" class="control-label col-sm-3">用户</label>
        <div class="col-sm-6">
        <?php echo \yii\jui\AutoComplete::widget([
                'name' => '用户',
                'id' => 'user-auto',
                'value' => empty($model->userid) ? '' : \common\models\User::findIdentity($model->userid)->username,
                'options' => ['class' => 'form-control'],
                'clientOptions' => [
                    'source' => $members,
                    'autoFill'=>true,
                    'select' => new JsExpression("function( event, ui ) {
                        $('#hanziusertask-userid').val(ui.item.id);
                     }")
                ],
             ]);
        ?>
        <div class="help-block help-block-error "></div>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'userid')?>

    <?= $form->field($model, 'task_type')->dropDownList(HanziUserTask::types(true)) ?>

    <?= $form->field($model, 'quality')->textInput() ?>

    <?= $form->field($model, 'remark')->textArea() ?>

    <div class="form-group">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
