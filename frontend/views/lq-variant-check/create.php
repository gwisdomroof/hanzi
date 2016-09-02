<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LqVariantCheck */

$this->title = Yii::t('frontend', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Lq Variant Checks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-check-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
