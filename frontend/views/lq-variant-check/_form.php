<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariantCheck */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lq-variant-check-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'source')->textInput() ?>

    <?= $form->field($model, 'pic_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'variant_code1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'belong_standard_word_code1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nor_var_type1')->textInput() ?>

    <?= $form->field($model, 'variant_code2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'belong_standard_word_code2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nor_var_type2')->textInput() ?>

    <?= $form->field($model, 'bconfirm')->textInput() ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
