<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziSplit */

$this->title = Yii::t('frontend', '回查（二次拆分）', [
    'modelClass' => 'HanziSplit',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'HanziSplits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//$this->params['breadcrumbs'][] = '当前积分：' . \common\models\HanziUserTask::getScore(Yii::$app->user->id);
?>
<div class="hanzi-create">

    <?php echo $this->render('_split', [
        'model' => $model,
        'seq' => $seq,
    ]) ?>

</div>
