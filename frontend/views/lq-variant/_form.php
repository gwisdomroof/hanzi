<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lq-variant-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'source')->textInput() ?>

    <?= $form->field($model, 'pic_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'variant_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'belong_standard_word_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nor_var_type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
