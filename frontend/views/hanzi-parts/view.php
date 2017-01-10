<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\HanziParts */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Parts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-parts-view">

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
            'part_type',
            'part_char',
            'part_pic_id',
            'src_chs_lib',
            'src_gb13000',
            'src_old_lqhanzi',
            'src_feijinchang',
            'src_hujingyu',
            'src_lqhanzi',
            'lqhanzi_sn',
            'is_redundant',
            'frequency_zhzk',
            'frequency',
            'is_split_part',
            'is_search_part',
            'replace_parts',
            'strokes',
            'stroke_order',
            'remark',
            'c_t',
            'u_t',
        ],
    ]) ?>

</div>
