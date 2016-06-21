<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziTask */

$taskName = $model->task_type == 1 ? '拆字' : '录入';
$this->title = Yii::t('frontend', "创建$taskName". "任务");
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-task-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
