<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziParts */

$this->title = Yii::t('frontend', '批量注册');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Parts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .msg {
        color: red;
        font-size: 12px;
        font-style: italic;
    }

</style>
<div class="hanzi-parts-create">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'id' => 'hanzi-form']); ?>

    <?= $form->field($model, 'source')->radioList(\common\models\HanziParts::sources()) ?>

    <?= $form->field($model, 'batch_part_chars')->textarea() ?>
    <?php
    if (!empty($result)) {
        if (!empty($result['existed'])) {
            echo "<div class = 'exist form-group'><label class='control-label col-sm-3'>已注册部件</label><div class='col-sm-6'>{$result['existed']}。</div></div>";
        }
        if (!empty($result['notExisted'])) {
            echo "<div class = 'not-exist form-group'><label class='control-label col-sm-3'>未注册部件</label><div class='col-sm-6'>{$result['notExisted']}。</div></div>";
        }
        if (!empty($result['msg'])) {
            echo "<div class = 'msg form-group'><label class='control-label col-sm-3'></label><div class='col-sm-6'>{$result['msg']}</div></div>";
        }
    }
    ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
            <?php echo Html::submitButton($model->isNewRecord ? Yii::t('frontend', '注册') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
