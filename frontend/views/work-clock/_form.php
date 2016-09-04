<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WorkClock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="work-clock-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'type', ['inputOptions' => ['style' => 'width:50%;']])->dropDownList(\common\models\WorkPackage::types(), ['disabled' => !$model->isNewRecord]) ?>

    <?= $form->field($model, 'content')->textArea(['maxlength' => true, 'style'=>'height:100px;']) ?>

    <div class="form-group">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
