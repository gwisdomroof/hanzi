<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HanziSet */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="hanzi-set-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'source')->textInput() ?>

    <?php echo $form->field($model, 'type')->textInput() ?>

    <?php echo $form->field($model, 'word')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'pic_name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'nor_var_type')->textInput() ?>

    <?php echo $form->field($model, 'belong_standard_word_code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'standard_word_code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'position_code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'bduplicate')->textInput() ?>

    <?php echo $form->field($model, 'duplicate_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'pinyin')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'radical')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'stocks')->textInput() ?>

    <?php echo $form->field($model, 'zhengma')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'wubi')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'structure')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'bhard')->textInput() ?>

    <?php echo $form->field($model, 'min_split')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'deform_split')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'similar_stock')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'max_split')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'mix_split')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'stock_serial')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'created_at')->textInput() ?>

    <?php echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
