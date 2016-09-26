<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lq-variant-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'source')->textInput() ?>

    <?= $form->field($model, 'word')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pic_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ori_pic_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nor_var_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'belong_standard_word_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-sm-9 col-sm-offset-3">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
