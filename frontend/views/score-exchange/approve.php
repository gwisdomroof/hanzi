<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\ScoreExchange;

/* @var $this yii\web\View */
/* @var $model common\models\ScoreExchange */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Score Exchange',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Score Exchanges'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>


<div class="score-exchange-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'user.username')->textInput(['readonly' => true, 'value' => $model->user->username]) ?>

    <?= $form->field($model, 'type')->dropDownList(ScoreExchange::typesWithScore(), ['prompt' => '', 'disabled' => true]) ?>

	<?= $form->field($model, 'status')->dropDownList(ScoreExchange::statuses(), ['prompt' => '']) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
    	<div class="col-lg-offset-3 col-lg-8">
        <?= Html::submitButton(Yii::t('frontend', '更新'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
