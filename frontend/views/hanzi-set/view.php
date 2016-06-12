<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\HanziSet */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzi Sets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hanzi-set-view">

    <p>
        <?php echo Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
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
            'duplicate',
            'duplicate_id',
            'pinyin',
            'radical',
            'stocks',
            'zhengma',
            'wubi',
            'structure',
            'bhard',
            'min_split',
            'deform_split',
            'similar_stock',
            'max_split',
            'mix_split',
            'stock_serial',
            'remark',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
