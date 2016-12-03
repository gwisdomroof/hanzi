<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GltwDedupResult */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Gltw Dedup Result',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Gltw Dedup Results'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="gltw-dedup-result-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
