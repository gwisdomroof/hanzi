<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Hanzi */

$this->title = Yii::t('frontend', '二次拆分', [
    'modelClass' => 'Hanzi',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-create">

    <?php echo $this->render('_split', [
        'model' => $model,
        'seq' => $seq,
    ]) ?>

</div>
