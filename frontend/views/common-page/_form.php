<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CommonPage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="common-page-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'enableClientValidation' => false
    ]); ?>

    <?= $form->field($model, 'page')->textInput() ?>

    <?= $form->field($model, 'task_type')->dropDownList(\common\models\HanziTask::types(), ['readonly' => true]) ?>

    <div class="form-group">
        <div class="col-sm-9 col-sm-offset-3">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
