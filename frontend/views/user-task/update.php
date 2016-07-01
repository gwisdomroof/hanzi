<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HanziUserTask */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Hanzi User Task',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi User Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="hanzi-user-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
