<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\HanziTask;
use common\models\user;


/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="hanzi-task-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <!-- <?= $form->field($model, 'leader_id', ['enableClientValidation'=>false])->textInput(['readonly' => 'readonly', 'value' => Yii::$app->user->identity->username]) ?> -->

    <div class="form-group field-hanzitask-leader">
        <?= Html::label('组长', null, ['class' => 'control-label col-sm-3']); ?>
        <div class="col-sm-6">
        <?= Html::textInput('leader_id', Yii::$app->user->identity->username, ['readonly' => true, 'class' => "form-control"]);?>
        </div>
    </div>

    <?= $form->field($model, 'seq')->dropDownList(HanziTask::seqs(), ['prompt' => '', 'disabled' => true]) ?>

    <?= $form->field($model, 'user_id')->dropDownList($model->members(), ['prompt' => '']) ?>

    <?= $form->field($model, 'page')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(HanziTask::statuses(), ['prompt' => '']) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
