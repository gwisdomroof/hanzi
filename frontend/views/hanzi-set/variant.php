<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\HanziSet;
?>

<style type="text/css">
    .container {
        width: 100%;
    }
    .zitou {
        background-image: url(/img/hanzi/bg.gif);
        background-size: 100%;
        background-repeat:no-repeat;
        text-align: center;
        height: 100px;
        line-height: 100px;
        width: 100px;
        margin: 0 auto;
        font-family: 楷体;
    }
    .zitou-img {
        width: 80%;
        font-size: auto;
    }
    .zitou-text {
        font-size: 90px;
    }
    html, body { height: 100% }
    
    iframe {
        border: 1px solid #eef;
    }

</style>



<div class="summary">
    <table class="table table-striped">
    <tbody>
        <tr>
            <th rowspan=<?=count($summary)+1?> style="width:150px; background-color:white; border-style: none none none none; vertical-align:top"><div class="zitou">
            <?php if (isset($zitouArr['zitou'])) {
                    if (mb_strlen($zitouArr['zitou'], 'UTF-8') > 1) {
                        $path = HanziSet::getPicturePath($zitouArr['source'], $zitouArr['zitou']);
                        echo Html::img($path, ['class' => 'zitou-img']);
                    } else {
                        echo "<div class='zitou-text'>" . $zitouArr['zitou'] . "</div>";
                    }
                }
            ?></div></th>
            <th>字典</th><th>原始位置</th><th>所属正字</th><th>备注</th>
        </tr>
        <?php foreach ($summary as $item) : ?>
            <tr><td><?=$item['source']?></td>
                <td><?=$item['position']?></td>
                <td><?=$item['standardWord']?></td>
                <td><?=$item['remark']?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
</div>
<hr/>

<div class="detail">
<ul id="myTab" class="nav nav-tabs">
    <?php 
    $index = 1;
    foreach ($data as $item) : ?>
        <li class=<?= $index++ == $active ? 'active': ''?>>
            <a name=<?=$item['key']?> href=<?='#' . $item['id']?> data-toggle="tab"><?=$item['key']?></a>
        </li>
    <?php endforeach; ?>
</ul>

<div id="myTabContent" class="tab-content">
    <?php 
    $index = 1;
    foreach ($data as $item)  {
        $class = $index++ == $active ? ' in active': '';
        echo "<div class='tab-pane fade " . $class . "' id='" . $item['id'] ."'>";

        if ($item['source'] == HanziSet::SOURCE_TAIWAN) { 
            echo "<iframe src='" . $item['url'] . "' style='width:100%; height: 800px;'></iframe>";
        } elseif ($item['source'] == HanziSet::SOURCE_HANYU) {
            echo "<iframe src='" . $item['url'] . "' style='position: absolute; width:100%; height: 100%; border: none'></iframe>";
        } elseif ($item['source'] == HanziSet::SOURCE_GAOLI) {
            echo "<iframe src='" . $item['url'] . "' style='position: absolute; width:100%; height: 100%; border: none'></iframe>";
        } elseif ($item['source'] == HanziSet::SOURCE_DUNHUANG) {
            echo "<iframe src='" . $item['url'] . "' style='position: absolute; width:100%; height: 100%; border: none'></iframe>";
        }
        
        echo "</div>";
    } ?>

</div>
</div>

<?php
$script = <<<SCRIPT
    $(document).on('click', '.summary a', function() {
        var name = $(this).text();
        $("a[name='" + name + "']").click()
    });

SCRIPT;
$this->registerJs($script, \yii\web\View::POS_END);
