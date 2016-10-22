<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GltwDedupResultSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gltw-dedup-result-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'source') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'word') ?>

    <?= $form->field($model, 'pic_name') ?>

    <?php // echo $form->field($model, 'nor_var_type') ?>

    <?php // echo $form->field($model, 'belong_standard_word_code') ?>

    <?php // echo $form->field($model, 'standard_word_code') ?>

    <?php // echo $form->field($model, 'position_code') ?>

    <?php // echo $form->field($model, 'duplicate_id1') ?>

    <?php // echo $form->field($model, 'duplicate_id2') ?>

    <?php // echo $form->field($model, 'duplicate_id3') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('frontend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
