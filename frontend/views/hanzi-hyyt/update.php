<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HanziHyyt */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Hanzi Hyyt',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Hyyts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="hanzi-hyyt-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
