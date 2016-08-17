<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariantCheckSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lq-variant-check-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'source') ?>

    <?= $form->field($model, 'pic_name') ?>

    <?= $form->field($model, 'variant_code1') ?>

    <?= $form->field($model, 'belong_standard_word_code1') ?>

    <?php // echo $form->field($model, 'nor_var_type1') ?>

    <?php // echo $form->field($model, 'level1') ?>

    <?php // echo $form->field($model, 'variant_code2') ?>

    <?php // echo $form->field($model, 'belong_standard_word_code2') ?>

    <?php // echo $form->field($model, 'nor_var_type2') ?>

    <?php // echo $form->field($model, 'level2') ?>

    <?php // echo $form->field($model, 'bconfirm') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('frontend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
