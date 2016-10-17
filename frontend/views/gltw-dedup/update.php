<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GltwDedup */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Gltw Dedup',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Gltw Dedups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="gltw-dedup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
