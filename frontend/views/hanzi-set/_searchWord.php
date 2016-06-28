<div class="summary" style="color:#808080; margin-left: 10px; font-style: italic;"><?=$message?></div>

<div style="word-wrap: break-word; word-break: normal; ">
<?php foreach ($data as $item) {
    if (!empty($item->word)) {
        echo "<span class='hanzi-item'>". $item->word . "</span>";
    } elseif (!empty($item->pic_name)) {
        $picPath = \common\models\HanziSet::getPicturePath($item->source, $item->pic_name);
        echo "<img alt= '$item->pic_name' src='$picPath' class='hanzi-img'>";
    }
} ?>
</div>

<div style="float:left;">
    <?=\yii\widgets\LinkPager::widget([
        'pagination' => $pagination,
        'options' => [
            'class' => 'pagination',
            ]
    ]);?>
</div>