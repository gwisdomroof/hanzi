<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WorkClock */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Work Clock',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Work Clocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="work-clock-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
