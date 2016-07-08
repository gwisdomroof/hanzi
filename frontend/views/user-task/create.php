<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziUserTask */

$this->title = Yii::t('frontend', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi User Tasks')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-user-task-create">

    <?= $this->render('_form', [
        'model' => $model,
        'members' => $members,
    ]) ?>

</div>
