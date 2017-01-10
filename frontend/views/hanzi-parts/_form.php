<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HanziParts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hanzi-parts-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'part_type')->textInput() ?>

    <?= $form->field($model, 'part_char')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'part_pic_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'src_chs_lib')->textInput() ?>

    <?= $form->field($model, 'src_gb13000')->textInput() ?>

    <?= $form->field($model, 'src_old_lqhanzi')->textInput() ?>

    <?= $form->field($model, 'src_feijinchang')->textInput() ?>

    <?= $form->field($model, 'src_hujingyu')->textInput() ?>

    <?= $form->field($model, 'src_lqhanzi')->textInput() ?>

    <?= $form->field($model, 'lqhanzi_sn')->textInput() ?>

    <?= $form->field($model, 'is_redundant')->textInput() ?>

    <?= $form->field($model, 'frequency_zhzk')->textInput() ?>

    <?= $form->field($model, 'frequency')->textInput() ?>

    <?= $form->field($model, 'is_split_part')->textInput() ?>

    <?= $form->field($model, 'is_search_part')->textInput() ?>

    <?= $form->field($model, 'replace_parts')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'strokes')->textInput() ?>

    <?= $form->field($model, 'stroke_order')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'c_t')->textInput() ?>

    <?= $form->field($model, 'u_t')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
