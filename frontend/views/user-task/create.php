<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziUserTask */

$this->title = Yii::t('frontend', 'Create Hanzi User Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi User Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-user-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
