<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use common\models\HanziSet;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CommonPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '两个及以上单笔部件集');
$this->params['breadcrumbs'][] = $this->title;
?>

    <style>
        .result {
            margin-top: 20px;
        }

        .hanzi-item {
            border: 1px solid #eef;
            margin: 2px;
            cursor: pointer;
            width: 45px;
            height: 45px;
            font-size: 35px;
        }

        .hanzi-img {
            width: 45px;
            height: 45px;
        }

        .empty {
            background-color: yellow;
        }

        .msg {
            font-size: 12px;
            font-style: italic;
            color: #888888;
            margin-left: 20px;
        }
    </style>

    <div class="hanzi-parts-index">

        <div class="msg">
            <?php if (!empty($models)) {
                echo "检索到{$pages->totalCount}条数据.";
            } ?>
        </div>

        <div class="col-sm-12 result">
            <?php
            foreach ($models as $variant) {
                $title = "{$variant->id}";
                $title .= "&#xa;初次：{$variant->initial_split11}|{$variant->initial_split12}|{$variant->deform_split10};";
                $title .= "&#xa;回查：{$variant->initial_split21}|{$variant->initial_split22}|{$variant->deform_split20};";

                if (!empty($variant->word)) {
                    echo "<span class='hanzi-item glyph' title='{$title}'  id='{$variant->id}'>{$variant->word}</span>";
                } elseif (!empty($variant->picture)) {
                    $picPath = HanziSet::getPicturePath($variant->source, $variant->picture);
                    echo "<span class='hanzi-item picture' title='{$title}' id='{$variant->id}'><img alt= '$variant->picture' src='$picPath' class='hanzi-img'></span>";
                } else {
                    echo "<span class='hanzi-item empty' title='{$title}'  id='{$variant->id}'>空</span>";
                }
            }
            ?>
        </div>

        <?= LinkPager::widget(['pagination' => $pages]); ?>

    </div>


<?php
$script = <<<SCRIPT
    $(document).on('click', '.hanzi-item', function() {
        var url = '/hanzi-split/determine/?id=' + $(this).attr('id');
        window.open(url);
    });
    

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);
