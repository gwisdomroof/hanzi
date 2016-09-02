<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WorkPackage */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Work Package',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Work Packages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="work-package-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
