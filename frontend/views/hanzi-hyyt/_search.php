<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\HanziHyytSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hanzi-hyyt-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'volume') ?>

    <?= $form->field($model, 'page') ?>

    <?= $form->field($model, 'num') ?>

    <?= $form->field($model, 'picture') ?>

    <?php // echo $form->field($model, 'word1') ?>

    <?php // echo $form->field($model, 'type1') ?>

    <?php // echo $form->field($model, 'tong_word1') ?>

    <?php // echo $form->field($model, 'zhushi1') ?>

    <?php // echo $form->field($model, 'word2') ?>

    <?php // echo $form->field($model, 'type2') ?>

    <?php // echo $form->field($model, 'tong_word2') ?>

    <?php // echo $form->field($model, 'zhushi2') ?>

    <?php // echo $form->field($model, 'word3') ?>

    <?php // echo $form->field($model, 'type3') ?>

    <?php // echo $form->field($model, 'tong_word3') ?>

    <?php // echo $form->field($model, 'zhushi3') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('frontend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
