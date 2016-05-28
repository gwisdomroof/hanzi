<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\MemberRelation;

/* @var $this yii\web\View */
/* @var $model common\models\MemberRelation */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="member-relation-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'leader_id')->dropDownList(MemberRelation::leaders(), ['prompt' => '请选择...']) ?>

    <?php echo $form->field($model, 'member_id')->dropDownList(MemberRelation::members(), ['prompt' => '请选择...']) ?>

    <?php echo $form->field($model, 'status')->dropDownList(MemberRelation::statuses()) ?>

    <?php echo $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
