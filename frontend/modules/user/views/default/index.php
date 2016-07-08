<?php

use trntv\filekit\widget\Upload;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('frontend', 'User Settings')
?>

<div class="user-profile-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="form-group">
        <label class="control-label col-sm-3">ID</label>
        <div class="control-label col-sm-6" style="text-align: left;"><?php echo Yii::$app->user->identity->id;?></div>
    </div>

    <hr>

    <?php echo $form->field($model->getModel('profile'), 'picture')->widget(
        Upload::classname(),
        [
            'url' => ['avatar-upload']
        ]
    )?>

    <?php echo $form->field($model->getModel('profile'), 'firstname')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model->getModel('profile'), 'middlename')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model->getModel('profile'), 'lastname')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model->getModel('profile'), 'locale')->dropDownlist(Yii::$app->params['availableLocales']) ?>

    <?php echo $form->field($model->getModel('profile'), 'gender')->dropDownlist([
        \common\models\UserProfile::GENDER_FEMALE => Yii::t('frontend', 'Female'),
        \common\models\UserProfile::GENDER_MALE => Yii::t('frontend', 'Male')
    ], ['prompt' => '']) ?>

    <hr>

    <?php echo $form->field($model->getModel('account'), 'username') ?>

    <?php echo $form->field($model->getModel('account'), 'email') ?>

    <?php echo $form->field($model->getModel('account'), 'password')->passwordInput() ?>

    <?php echo $form->field($model->getModel('account'), 'password_confirm')->passwordInput() ?>

    <div class="form-group">
        <label class="control-label col-sm-3">&nbsp;</label>
        <div class="col-sm-6">
        <?php echo Html::submitButton(Yii::t('frontend', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
