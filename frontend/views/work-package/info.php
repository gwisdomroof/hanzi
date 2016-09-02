<?php

use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel common\models\WorkPackageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Work Packages');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="info">
    <span class="msg" style="font-size: 16px"><?= $msg ?></span>
    <span class="<?=empty($url)?'hidden':null?>" style="color: #8a8a8a; font-size: 12px;"><span id="autoDir" style="color: #337ab7"></span> 秒后跳转……</span>
</div>

<?php
$script = <<<SCRIPT
    var num = 5;
    function countDown() {
        if (num >= 0) {
            var str = '';
            str += num;
            document.getElementById('autoDir').innerHTML = str;
            num--;
            setTimeout(countDown, 1000);
        }
        else {
        
            window.location.href = "{$url}";
        }
    }
    
        window.onload = countDown;
SCRIPT;
if ($url != null)
    $this->registerJs($script, \yii\web\View::POS_END);
