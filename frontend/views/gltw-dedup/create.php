<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GltwDedup */

$this->title = Yii::t('frontend', 'Create Gltw Dedup');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Gltw Dedups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gltw-dedup-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
