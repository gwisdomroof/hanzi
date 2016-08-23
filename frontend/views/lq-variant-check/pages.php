<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LqVariantCheckSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '异体字判定总页面');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .page {
        margin: 5px 5px;
        border: 1px solid #eef;
        width: 30px;
        float: left;
        text-align: center;
    }
</style>

<div style="font-size:16px; text-align:center;" class="col-sm-offset-1 ol-sm-10">
    <?php
        for ($i=1; $i <= 189 ; $i++) { 
            $url = Url::toRoute(['lq-variant-check/admin', 'page' => $i]);
            echo "<div class='page'><a target='_blank' href='$url'>$i</a></div>";
            if ($i%20 == 0) {
                echo "<div class='clearfix'></div>";
            }
        }
    ?>
</div>

