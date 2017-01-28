<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\HanziSet;

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
            $picPath = HanziSet::getPicturePath($variant->source, $normal);
            echo "<div><img alt= '$normal' src='$picPath' class='zitou-img'></div>";
            echo "<span class='yiti-label'>異體字：</span>";
        }

        if (!empty($variant->pic_name)) {
            $picPath = HanziSet::getPicturePath($variant->source, $variant->pic_name);
            $title = "";
            if (!empty($variant->pic_name)) {
                $title .= "编号：{$variant->pic_name}&#xa;";
            }
            if (!empty($variant->word)) {
                $title .= "文字：{$variant->word}&#xa;";
            }
            if (!empty($variant->belong_standard_word_code)) {
                $title .= "所属正字：{$variant->belong_standard_word_code}&#xa;";
            }
            if (!empty($variant->nor_var_type)) {
                $title = $title . "正异类型：" . HanziSet::norVarTypes()[$variant->nor_var_type] . "&#xa;";
            }
            if (!empty($variant->korean_dup_hanzi)) {
                $title .= "高丽重复编号：{$variant->korean_dup_hanzi}&#xa;";
            }
            if (!empty($variant->duplicate_id)) {
                $title .= "重复编号：{$variant->duplicate_id}&#xa;";
            }
            $title = trim($title, '&#xa;');

            $searchField = $variant->word . $variant->pic_name . $variant->position_code . $variant->korean_dup_hanzi . $variant->duplicate_id;
            $class = (strpos($searchField, $param) !== false) ? 'hanzi-img param variant' . $variant->nor_var_type : 'hanzi-img variant' . $variant->nor_var_type;
            echo "<span class='hanzi-item' ><a target='_blank' class='$class' title='$title' href='" . Url::toRoute(['hanzi-dict/variant', 'param' => $variant->pic_name]) . "'>" . "<img alt= '$variant->pic_name' src='$picPath' class='hanzi-img'></a></span>";
        } elseif (!empty($variant->word)) {
            echo "<span class='hanzi-item'>" . $variant->word . "</span>";
        }

    } ?>
</div>
