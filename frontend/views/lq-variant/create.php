<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LqVariant */

$this->title = Yii::t('frontend', 'Create Lq Variant');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Lq Variants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
