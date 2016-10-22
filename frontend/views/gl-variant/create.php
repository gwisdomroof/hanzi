<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GltwDedupResult */

$this->title = Yii::t('frontend', 'Create Gltw Dedup Result');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Gltw Dedup Results'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gltw-dedup-result-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
