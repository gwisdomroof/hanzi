<?php

use common\models\HanziSplit;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\HanziSplitSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="hanzi-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'post',
        'layout' => 'horizontal',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'source') ?>

    <?php echo $form->field($model, 'hanzi_type')->dropDownList(HanziSplit::types(), ['prompt' => '']) ?>

    <?php echo $form->field($model, 'word') ?>

    <?php echo $form->field($model, 'picture') ?>

    <?php // echo $form->field($model, 'nor_var_type') ?>

    <?php // echo $form->field($model, 'standard_word') ?>

    <?php // echo $form->field($model, 'position_code') ?>

    <?php // echo $form->field($model, 'radical') ?>

    <?php // echo $form->field($model, 'stocks') ?>

    <?php // echo $form->field($model, 'structure') ?>

    <?php // echo $form->field($model, 'corners') ?>

    <?php // echo $form->field($model, 'attach') ?>

    <?php // echo $form->field($model, 'hard10') ?>

    <?php // echo $form->field($model, 'initial_split11') ?>

    <?php // echo $form->field($model, 'initial_split12') ?>

    <?php // echo $form->field($model, 'deform_split10') ?>

    <?php // echo $form->field($model, 'similar_stock10') ?>

    <?php // echo $form->field($model, 'hard20') ?>

    <?php // echo $form->field($model, 'initial_split21') ?>

    <?php // echo $form->field($model, 'initial_split22') ?>

    <?php // echo $form->field($model, 'deform_split20') ?>

    <?php // echo $form->field($model, 'similar_stock20') ?>

    <?php // echo $form->field($model, 'hard30') ?>

    <?php // echo $form->field($model, 'initial_split31') ?>

    <?php // echo $form->field($model, 'initial_split32') ?>

    <?php // echo $form->field($model, 'deform_split30') ?>

    <?php // echo $form->field($model, 'similar_stock30') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
