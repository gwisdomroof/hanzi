<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GltwDedupResult */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gltw-dedup-result-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'source')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'word')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pic_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nor_var_type')->textInput() ?>

    <?= $form->field($model, 'belong_standard_word_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'standard_word_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'duplicate_id1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'duplicate_id2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'duplicate_id3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
            <?php echo Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
