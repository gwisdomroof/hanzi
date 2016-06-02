<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Hanzi */

$this->title = Yii::t('frontend', 'Create {modelClass}', [
    'modelClass' => 'Hanzi',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-create">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
