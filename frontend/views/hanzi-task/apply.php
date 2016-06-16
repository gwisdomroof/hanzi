<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\HanziTask;
use common\models\user;


/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */

$this->title = Yii::t('frontend', 'Apply Hanzi Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-task-apply">
	
	<div class="hanzi-task-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <!-- <?= $form->field($model, 'leader_id', ['enableClientValidation'=>false])->textInput(['readonly' => 'readonly', 'value' => Yii::$app->user->identity->username]) ?> -->

    <?= $form->field($model, 'leader_id')->dropDownList([$model->leader->id => $model->leader->username], ['readonly' => true]) ?>

    <?= $form->field($model, 'seq')->dropDownList(HanziTask::seqs(), ['readonly' => true]) ?>

    <?= $form->field($model, 'user_id')->dropDownList([Yii::$app->user->id => Yii::$app->user->identity->username], ['readonly' => true]) ?>

    <?= $form->field($model, 'page')->dropDownList(HanziTask::getIdlePages()) ?>

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
