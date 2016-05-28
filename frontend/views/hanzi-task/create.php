<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */

$this->title = Yii::t('frontend', 'Create Hanzi Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-task-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
