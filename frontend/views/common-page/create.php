<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CommonPage */

$this->title = Yii::t('frontend', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Common Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="common-page-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
