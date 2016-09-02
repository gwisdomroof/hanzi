<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WorkClock */

$this->title = Yii::t('frontend', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Work Clocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-clock-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
