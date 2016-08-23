<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziSplit */

$this->title = Yii::t('frontend', 'Create {modelClass}', [
    'modelClass' => 'HanziSplit',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'HanziSplits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-create">

    <?php echo $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
