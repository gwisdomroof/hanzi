<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HanziHyyt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hanzi-hyyt-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'volume')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'page')->textInput() ?>

    <?= $form->field($model, 'num')->textInput() ?>

    <?= $form->field($model, 'picture')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'word1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type1')->textInput() ?>

    <?= $form->field($model, 'tong_word1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zhushi1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'word2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type2')->textInput() ?>

    <?= $form->field($model, 'tong_word2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zhushi2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'word3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type3')->textInput() ?>

    <?= $form->field($model, 'tong_word3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zhushi3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
