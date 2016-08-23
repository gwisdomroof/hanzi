<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariant */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Lq Variant',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Lq Variants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="lq-variant-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
