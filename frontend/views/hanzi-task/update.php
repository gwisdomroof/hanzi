<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Hanzi Task',
]) . $model->id;

$taskName = $model->task_type == 1 ? '拆字' : '录入';

$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', "更新$taskName". "任务");
?>

<div class="hanzi-task-update">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
