<?php
/* @var $this yii\web\View */
$this->title = Yii::$app->name;
?>
<div class="site-index">

    <!-- <?php echo \common\widgets\DbCarousel::widget([
        'key'=>'index',
        'options' => [
            'class' => 'slide', // enables slide effect
        ],
    ]) ?> -->

    <div class="jumbotron">
        <div class="jumbotron">
            <p><a href="<?php echo yii\helpers\Url::to(['hanzi-split/index']);?>" class="btn btn-lg btn-success">欢迎来到龙泉大藏经</a></p>
        </div>
    </div>
</div>
