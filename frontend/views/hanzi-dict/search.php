<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\search\HanziSetSearch;


/* @var $this yii\web\View */
/* @var $searchModel common\models\hanziSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', '异体字检索');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', '异体字检索'), 'url' => ['ysearch']];

?>

<div class="hanzi-set-ids-index">
    <div class="search-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'method' => 'post',
            'enableClientScript' => false,
            'enableClientValidation' => false
        ]); ?>

        <?= $form->field($hanziSearch, 'param', ['template' => "{input}\n{hint}\n{error}", 'options' => ['class' => 'col-sm-4']])->textInput(['maxlength' => true, 'placeholder' => "请输入文字或图片字编号…"]) ?>

        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>

        <?php ActiveForm::end(); ?>

        <br/>

        <div class="search-result">
            <?php echo $this->render('_search', [
                'hanziSearch' => $hanziSearch,
                'hanziSet' => $hanziSet,
                'lqVariants' => $lqVariants,
                'param' => $hanziSearch->param
            ]);
            ?>
        </div>

    </div>

</div>

