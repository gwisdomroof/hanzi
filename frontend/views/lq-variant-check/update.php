<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariantCheck */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Lq Variant Check',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Lq Variant Checks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="lq-variant-check-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
