<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\WorkPackage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Work Packages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="work-package-view col-sm-10">
    <p>
        <?= Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'type',
            'volume',
            'daily_schedule',
            [
                'attribute' => 'expected_date',
                'format'=>['datetime','php:Y-m-d'],
            ],
            'progress',
            [
                'attribute' => 'created_at',
                'format'=>['datetime','php:Y-m-d'],
            ],
        ],
    ]) ?>

</div>

<div class="col-sm-2 pull-right">
    <nav class="navbar" style="background-color: #FFF8DC; border: 1px solid #E0DCBF;">
        <ul class="nav">
            <li class="active"><a href="<?= Url::toRoute(['create']) ?>">领任务</a></li>
            <li><a href="<?= Url::toRoute(['index']) ?>">进行中</a></li>
            <li><a href="<?= Url::toRoute(['index','progress'=>'finish']) ?>">已完成</a></li>
            <li><a href="<?= Url::toRoute(['user-task/index']) ?>">详　情</a></li>
        </ul>
    </nav>
</div>