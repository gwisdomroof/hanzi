<?php
use yii\helpers\Url;
?>

<div class="summary" style="color:#808080; margin-left: 10px; font-style: italic;"><?=$message?></div>

<div style="word-wrap: break-word; word-break: normal; ">
<?php foreach ($data as $item) {
    $title = "部首：{$item->radical}&#xa;笔画：{$item->max_stroke}&#xa;初步拆分：{$item->min_split}";
    if (!empty($item->word)) {
        echo "<a target='_blank' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $item->word]) ."'>" . "<span class='hanzi-item'>". $item->word . "</span>" . "</a>";
    } elseif (!empty($item->pic_name)) {
        $picPath = \common\models\HanziSet::getPicturePath($item->source, $item->pic_name);
        echo "<a target='_blank' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $item->pic_name]) ."'>" . "<img alt= '$item->pic_name' src='$picPath' class='hanzi-img'>" . "</a>";
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