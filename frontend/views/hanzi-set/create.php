<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziSet */

$this->title = Yii::t('frontend', 'Create {modelClass}', [
    'modelClass' => 'Hanzi Set',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Sets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-set-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
