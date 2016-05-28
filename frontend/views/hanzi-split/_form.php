<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Hanzi;

/* @var $this yii\web\View */
/* @var $model common\models\Hanzi */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="hanzi-form">
    
    <?php $readonly = false; ?>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'id' => 'hanzi-form']); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'source')->dropDownList(Hanzi::sources(), ['prompt' => '']) ?>

    <?php echo $form->field($model, 'hanzi_type')->dropDownList(Hanzi::types(), ['prompt' => '']) ?>

    <?php echo $form->field($model, 'word')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'picture')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'nor_var_type')->dropDownList(Hanzi::norVarTypes(), ['prompt' => '']) ?>

    <?php echo $form->field($model, 'standard_word')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'position_code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'radical')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'stocks')->textInput() ?>

    <?php echo $form->field($model, 'structure')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'hard10')->dropDownList(['0' => '否', '1' => '是'], ['disabled' => $readonly]) ?>

    <?php echo $form->field($model, 'initial_split11')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'initial_split12')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'deform_split10')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'similar_stock10')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'hard20')->dropDownList(['0' => '否', '1' => '是'], ['disabled' => $readonly]) ?>

    <?php echo $form->field($model, 'initial_split21')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'initial_split22')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'deform_split20')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'similar_stock20')->textInput(['maxlength' => true, 'readonly' => $readonly]) ?>

    <?php echo $form->field($model, 'hard30')->dropDownList(['0' => '否', '1' => '是']) ?>

    <?php echo $form->field($model, 'initial_split31')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'initial_split32')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'deform_split30')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'similar_stock30')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <!-- <?php echo $form->field($model, 'created_at')->textInput() ?>

    <?php echo $form->field($model, 'updated_at')->textInput() ?> -->

    <input type="hidden" id="next" name="next" value=false> 

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

 <?php
$script = <<<SCRIPT
    $('#next-button').click(function() {
        $('#next').val(true);
        $('#hanzi-form').submit() 
    });
SCRIPT;
$this->registerJs($script, \yii\web\View::POS_END);
