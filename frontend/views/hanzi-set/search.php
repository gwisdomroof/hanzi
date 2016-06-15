<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;


/* @var $this yii\web\View */
/* @var $searchModel common\models\hanziSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'hanzi-set');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'hanzi-set'), 'url' => ['search']];
$this->params['breadcrumbs'][] = '部件笔画检字法';

?>

<style type="text/css">
    .hanzi-img {
        /*border: 1px solid #eee;*/
        border-collapse: collapse;
        display: inline-table;
        font-size: 28px;
        width: 35px;
        height: 35px;
        margin: 1px 1px;
        padding: 2px 0px;
        vertical-align: middle;
    }

    .hanzi-item {
        /*border: 1px solid #eee;*/
        color: #003399;
        width: 35px;
        height: 35px;
        font-size: 28px;
        display: inline-table;
        vertical-align: middle;
        margin: 1px 1px;
        padding: 2px 0px;
        border-collapse: collapse;;
    }
</style>



<div class="hanzi-set-ids-index">
    <div class="hanzi-set-ids-form col-sm-6">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'method' => 'post',
            'enableClientScript' => false,
            'enableClientValidation' => false
        ]); ?>

        <?= $form->field($hanziSearch, 'param', ['template' => "{input}\n{hint}\n{error}", 'options' => ['class' => 'col-sm-8']])->textInput(['maxlength' => true]) ?> 
    
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?> 
        <?= Html::Button('清空', ['class' => 'btn btn-secondary ', 'id' => 'searchIds-clear']) ?>

        <?php ActiveForm::end(); ?>  

        <br/>

        <div class="summary" style="color:#808080; margin-left: 10px; font-style: italic;"><?=$message?></div>

        <div style="word-wrap: break-word; word-break: normal; ">

        <?php foreach ($data as $item) {
            if (!empty($item->word)) {
                echo "<span class='hanzi-item'>". $item->word . "</span>";
            } elseif (!empty($item->pic_name)) {
                $picPath = \common\models\HanziSet::getPicturePath($item->source, $item->pic_name);
                echo "<img alt= '$item->pic_name' src='$picPath' class='hanzi-img'>";
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

    </div>

</div>

<?php echo $this->render('..\hanzi-part\component') ?>


 
