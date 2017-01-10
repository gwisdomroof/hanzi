<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziParts */

$this->title = Yii::t('frontend', 'Create Hanzi Parts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Parts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-parts-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
