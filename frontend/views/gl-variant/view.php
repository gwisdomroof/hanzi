<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GltwDedupResult */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Gltw Dedup Results'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gltw-dedup-result-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'id',
            'source',
            'type',
            'word',
            'pic_name',
            'nor_var_type',
            'belong_standard_word_code',
            'standard_word_code',
            'position_code',
            'duplicate_id1',
            'duplicate_id2',
            'duplicate_id3',
            'remark',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
