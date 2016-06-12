<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\yann\assets\YannAssets;

?>
<div class="comment-default-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'hash',
            'content:ntext',
            'parent_id',
            'level',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>