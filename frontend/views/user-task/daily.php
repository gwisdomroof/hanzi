<?php

use yii\helpers\Html;
use common\models\HanziTask;
use common\models\HanziUserTask;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\HanziUserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Tasks Daily');
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .hasDatepicker {
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
    }

</style>


<div class="hanzi-user-task-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'enableClientValidation' => false
    ]); ?>
    <div class="col-sm-4">
        <?= $form->field($model, 'task_type')->dropDownList([
            HanziUserTask::TYPE_SPLIT => Yii::t('common', '异体字拆字'),
            HanziUserTask::TYPE_INPUT => Yii::t('common', '异体字录入'),
        ], ['prompt' => '']) ?>
    </div>
    <div class="col-sm-5">
        <label class="control-label col-sm-3" for="hanziusertasksearch-updated_at">日期</label>
        <?= $form->field($model, 'updated_at', ['labelOptions'=>['style'=>'display:none']])->widget(\yii\jui\DatePicker::classname(), [
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>
    </div>
    <div class="form-group col-sm-3">
        <?= Html::submitButton('查找', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="clearfix"></div>

<div class="hanzi-user-task-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'userid',
            'user.username',
            [
                'attribute' => 'cnt',
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],
        ],
    ]); ?>
</div>
