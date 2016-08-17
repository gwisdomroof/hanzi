<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariantSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lq-variant-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'source') ?>

    <?= $form->field($model, 'pic_name') ?>

    <?= $form->field($model, 'variant_code') ?>

    <?= $form->field($model, 'belong_standard_word_code') ?>

    <?php // echo $form->field($model, 'nor_var_type') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('frontend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
