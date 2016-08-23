<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HanziSplit */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'HanziSplit',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'HanziSplits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="hanzi-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
