<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HanziUserTask */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Hanzi User Task',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi User Tasks')];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="hanzi-user-task-update">

    <?= $this->render('_form', [
        'model' => $model,
        'members' => $members,
    ]) ?>

</div>
