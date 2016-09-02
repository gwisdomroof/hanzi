<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WorkPackage */

$this->title = Yii::t('frontend', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Work Packages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-package-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
