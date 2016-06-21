<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\HanziTask;
use common\models\user;


/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */
$taskName = $model->task_type == 1 ? '拆字' : '录入';
$this->title = Yii::t('frontend', "申请$taskName". "任务");
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="hanzi-task-apply">
	
	<div class="hanzi-task-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <!-- <?= $form->field($model, 'leader_id', ['enableClientValidation'=>false])->textInput(['readonly' => 'readonly', 'value' => Yii::$app->user->identity->username]) ?> -->

    <?= $form->field($model, 'task_type')->dropDownList(HanziTask::types(), ['readonly' => true]) ?>

    <?= $form->field($model, 'leader_id')->dropDownList([$model->leader->id => $model->leader->username], ['readonly' => true]) ?>

    <?= $form->field($model, 'user_id')->dropDownList([Yii::$app->user->id => Yii::$app->user->identity->username], ['readonly' => true]) ?>

    <?= $form->field($model, 'page')->dropDownList(HanziTask::getIdlePages($model->task_type)) ?>

    <?= $form->field($model, 'status')->dropDownList(HanziTask::statuses(), ['readonly' => true]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

	</div>

</div>
