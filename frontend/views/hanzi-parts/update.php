<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HanziParts */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Hanzi Parts',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Parts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="hanzi-parts-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
