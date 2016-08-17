<?php

use yii\helpers\Html;

?>

<style type="text/css">
    .hanzi-normal {
        font-size: 50px;
        font-family: Tripitaka UniCode;
        width: 55px;
        height: 60px;
        line-height: 1.1;
        text-align: center;
        border: 1px solid #eef;
    }
    .zitou-img {
        width: 75px;
        border: 1px solid #eef;
    }
    .hanzi-img {
        width: 45px;
        height: 45px;
    }

    .yiti-label {
        color: #777;
    }
    .gaoli {
        margin: 5px;
    }

</style>

<div class="gaoli">
<?php 
$normal = '';
foreach ($models as $variant) {
	if ($variant->belong_standard_word_code != $normal) {
		$normal = $variant->belong_standard_word_code;
        $picPath = \common\models\HanziSet::getPicturePath($variant->source, $normal);
        echo "<div><img alt= '$normal' src='$picPath' class='zitou-img'></div>";

		// echo "<div class='hanzi-normal'>". $normal . "</div>";
		echo "<span class='yiti-label'>異體字：</span>";
	}

    if (!empty($variant->word)) {
        echo "<span class='hanzi-item'>". $variant->word . "</span>";
    } elseif (!empty($variant->pic_name)) {
        $picPath = \common\models\HanziSet::getPicturePath($variant->source, $variant->pic_name);
        echo "<img alt='$variant->pic_name' title='$variant->pic_name' src='$picPath' class='hanzi-img'>";
    }
}  ?>
</div>
