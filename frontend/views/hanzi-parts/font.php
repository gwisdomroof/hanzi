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
    .fontOne {
        font-family: 部件集集外字;
        font-size: 32px;
        margin: 5px;
        border: 1px solid #eef;
    }

    .fontTwo {
        font-family: lqhanzi;
        margin-left: 10px;
        margin-right: 50px;
    }

    .msg {
        color: #7a869d;
        font-style: italic;
        margin-top: 5px;
        margin-bottom: 10px;
    }

</style>

<div class="hanzi-parts-index">

    <div class="col-sm-6">
        <div class='msg'>
            <?php if (!empty($fontOne)) {
                $count = count($fontOne);
                echo "共{$count}条记录。";
            } ?>
        </div>
        <div>
            <?php foreach ($fontOne as $value) {
                echo "<span class='fontOne'>$value</span>";
            } ?>
        </div>
    </div>

</div>
