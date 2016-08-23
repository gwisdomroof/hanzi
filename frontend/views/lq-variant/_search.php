<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LqVariantSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lq-variant-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'source') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'word') ?>

    <?= $form->field($model, 'pic_name') ?>

    <?php // echo $form->field($model, 'nor_var_type') ?>

    <?php // echo $form->field($model, 'belong_standard_word_code') ?>

    <?php // echo $form->field($model, 'standard_word_code') ?>

    <?php // echo $form->field($model, 'position_code') ?>

    <?php // echo $form->field($model, 'duplicate') ?>

    <?php // echo $form->field($model, 'duplicate_id') ?>

    <?php // echo $form->field($model, 'frequence') ?>

    <?php // echo $form->field($model, 'sutra_ids') ?>

    <?php // echo $form->field($model, 'bconfirm') ?>

    <?php // echo $form->field($model, 'pinyin') ?>

    <?php // echo $form->field($model, 'radical') ?>

    <?php // echo $form->field($model, 'stocks') ?>

    <?php // echo $form->field($model, 'zhengma') ?>

    <?php // echo $form->field($model, 'wubi') ?>

    <?php // echo $form->field($model, 'structure') ?>

    <?php // echo $form->field($model, 'bhard') ?>

    <?php // echo $form->field($model, 'min_split') ?>

    <?php // echo $form->field($model, 'deform_split') ?>

    <?php // echo $form->field($model, 'similar_stock') ?>

    <?php // echo $form->field($model, 'max_split') ?>

    <?php // echo $form->field($model, 'mix_split') ?>

    <?php // echo $form->field($model, 'stock_serial') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('frontend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
