<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziSplit */

$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'HanziSplits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = '启动回查';
?>
<div class="hanzi-stage" style="text-align: center">

    <form method="post">
        <input type="hidden" name="stage" value="1"/>
        <input type="hidden" name="_csrf" value="<?= Yii::$app->getRequest()->getCsrfToken();?>" />
        <button type="submit" class="btn btn-primary">启动回查阶段</button>
    </form>

    <div style="color: red; margin-top: 10px"><?=$msg?></div>

</div>
