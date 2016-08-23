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

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'word')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pic_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nor_var_type')->textInput() ?>

    <?= $form->field($model, 'belong_standard_word_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'standard_word_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'duplicate')->textInput() ?>

    <?= $form->field($model, 'duplicate_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'frequence')->textInput() ?>

    <?= $form->field($model, 'sutra_ids')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bconfirm')->textInput() ?>

    <?= $form->field($model, 'pinyin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'radical')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stocks')->textInput() ?>

    <?= $form->field($model, 'zhengma')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wubi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'structure')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bhard')->textInput() ?>

    <?= $form->field($model, 'min_split')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'deform_split')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'similar_stock')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'max_split')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mix_split')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stock_serial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
