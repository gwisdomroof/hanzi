<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HanziPartsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hanzi-parts-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'part_type') ?>

    <?= $form->field($model, 'part_char') ?>

    <?= $form->field($model, 'part_pic_id') ?>

    <?= $form->field($model, 'src_chs_lib') ?>

    <?php // echo $form->field($model, 'src_gb13000') ?>

    <?php // echo $form->field($model, 'src_old_lqhanzi') ?>

    <?php // echo $form->field($model, 'src_feijinchang') ?>

    <?php // echo $form->field($model, 'src_hujingyu') ?>

    <?php // echo $form->field($model, 'src_lqhanzi') ?>

    <?php // echo $form->field($model, 'lqhanzi_sn') ?>

    <?php // echo $form->field($model, 'is_redundant') ?>

    <?php // echo $form->field($model, 'frequency_zhzk') ?>

    <?php // echo $form->field($model, 'frequency') ?>

    <?php // echo $form->field($model, 'is_split_part') ?>

    <?php // echo $form->field($model, 'is_search_part') ?>

    <?php // echo $form->field($model, 'replace_parts') ?>

    <?php // echo $form->field($model, 'strokes') ?>

    <?php // echo $form->field($model, 'stroke_order') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'c_t') ?>

    <?php // echo $form->field($model, 'u_t') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('frontend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
