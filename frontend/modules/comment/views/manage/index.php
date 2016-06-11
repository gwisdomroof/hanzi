<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\yann\assets\YannAssets;

YannAssets::register($this);
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
<?php

use app\modules\yann\widgets\CommentWidget;

$username = Yii::$app->user->identity['username'];
echo CommentWidget::widget(['username' => $username]);
?>
<div id="commentWrap"></div>
<?php
$script = <<<SCRIPT
    new Comment({
        el: '#commentWrap',
        username: '{$username}',
    })
SCRIPT;
$this->registerJs($script, \yii\web\View::POS_END);