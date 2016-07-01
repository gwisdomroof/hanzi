<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HanziUserTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hanzi-user-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task_type')->textInput() ?>

    <?= $form->field($model, 'task_seq')->textInput() ?>

    <?= $form->field($model, 'task_status')->textInput() ?>

    <?= $form->field($model, 'quality')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
