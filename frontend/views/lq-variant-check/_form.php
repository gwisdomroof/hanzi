<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\LqVariantCheck;
use common\models\LqVariant;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariantCheck */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lq-variant-check-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'source')->dropDownList(LqVariant::sources()) ?>

    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <?= $form->field($model, 'variant_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'belong_standard_word_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nor_var_type')->dropDownList(\common\models\HanziSet::norVarTypes()) ?>

    <?= $form->field($model, 'remark')->textArea(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
