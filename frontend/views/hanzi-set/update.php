<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HanziSet */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Hanzi Set',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Sets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="hanzi-set-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
