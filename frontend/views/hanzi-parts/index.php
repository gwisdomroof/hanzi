<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\HanziPartsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Hanzi Parts');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .result {
        border: 1px solid #eef;
        margin: 2px;
        width: 40px;
        height: 40px;
        font-size: 30px;
    }

    .search-result {
        /*margin-left: 10px;*/
        /*margin-right: 50px;*/
        /*width: 80%;*/
    }

    .msg {
        color: #7a869d;
        font-style: italic;
        margin-top: 5px;
        margin-bottom: 10px;
    }

</style>

<div class="hanzi-parts-index">

    <form class="form-horizontal" action="/hanzi-parts/index" method="post">
        <input type="hidden" name="_csrf" value="bzgzVlZCNWQ6aVkjPjB7Plp1XCQdMAJRBw5rHCIPcR07cGsGGDBDOw==">
        <label class="control-label col-sm-1">来源</label>
        <div class="col-sm-2">
            <?php echo Html::dropDownList('source', $source, \common\models\HanziParts::sources(), ['class' => 'form-control col-sm-2', 'prompt' => '--请选择--']);
            ?>
        </div>
        <label class="control-label col-sm-1">笔画笔顺</label>
        <div class="col-sm-4">
            <input type="text" id="hanzi-parts-search" class="form-control" name="param" value="<?= $param ?>" placeholder="请输入笔画及笔顺...">
        </div>
        <button type="submit" class="btn btn-primary">检索</button>
    </form>

    <div class="search-result col-sm-12">
        <div class='msg'>
            <?php if (!empty($result)) {
                $count = count($result);
                echo "检索到{$count}条记录。";
            } ?>
        </div>

        <?php foreach ($result as $r) {
            echo "<span class='result'>$r</span>";
        } ?>
    </div>

</div>
