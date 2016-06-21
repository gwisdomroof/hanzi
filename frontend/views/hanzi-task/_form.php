<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\HanziTask;
use common\models\user;


/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="hanzi-task-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'task_type')->dropDownList(HanziTask::types(), ['readonly' => true]) ?>

    <?= $form->field($model, 'leader_id')->dropDownList([$model->leader->id => $model->leader->username], ['readonly' => true]) ?>

    <!-- <?= $form->field($model, 'seq')->dropDownList(HanziTask::seqs(), ['prompt' => '', 'disabled' => true]) ?> -->

    <?= $form->field($model, 'user_id')->dropDownList($model->members(), ['prompt' => '', 'disabled' => $model->leader_id == Yii::$app->user->id ? false : true]) ?>

    <?php
    $idlePages = HanziTask::getIdlePages($model->task_type);
    $pageArr =  isset($model->page) ? [$model->page => $model->page] + $idlePages : $idlePages;
    echo $form->field($model, 'page')->dropDownList($pageArr, ['prompt' => '', 'disabled' => $model->leader_id == Yii::$app->user->id ? false : true]) ?>

    <?= $form->field($model, 'status')->dropDownList(HanziTask::statuses()) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
