<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LqVariantCheck */

$this->title = Yii::t('frontend', 'Create Lq Variant Check');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Lq Variant Checks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-check-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
