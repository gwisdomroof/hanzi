<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziHyyt */

$this->title = Yii::t('frontend', 'Create Hanzi Hyyt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Hyyts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-hyyt-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
