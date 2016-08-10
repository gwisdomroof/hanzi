<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\ScoreExchange;


/* @var $this yii\web\View */
/* @var $model common\models\ScoreExchange */

$this->title = Yii::t('frontend', '申请奖品');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Score Exchanges'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
   
<div class="score-exchange-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'type')->dropDownList(ScoreExchange::typesWithScore(), ['prompt' => '']) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
    	<div class="col-lg-offset-3 col-lg-8">
        <?= Html::submitButton(Yii::t('frontend', '申请'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

