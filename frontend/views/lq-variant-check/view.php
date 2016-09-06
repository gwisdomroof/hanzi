<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\LqVariantCheck;
use common\models\LqVariant;

/* @var $this yii\web\View */
/* @var $model common\models\LqVariantCheck */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Lq Variant Checks'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lq-variant-check-view">

    <p>
        <?= Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user.username',
            [
                'attribute' => 'source',
                'value' => empty($model->source)? '' : LqVariant::sources()[$model->source],
            ],
            'pic_name',
            'variant_code',
            'belong_standard_word_code',
            [
                'attribute' => 'nor_var_type',
                'value' => empty($model->nor_var_type)? '' : \common\models\HanziSet::norVarTypes()[$model->nor_var_type],
            ],
            [
                'attribute' => 'level',
                'value' => empty($model->level)? '' : LqVariantCheck::levels()[$model->level],
            ],
            [
                'attribute' => 'bconfirm',
                'value' => !isset($model->bconfirm) ? '' : ($model->bconfirm == 1 ? '是' : '否'),
            ],
            'remark',
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:Y-m-d H:i:s'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['datetime', 'php:Y-m-d H:i:s'],
            ],
        ],
    ]) ?>

</div>
