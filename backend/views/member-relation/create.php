<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MemberRelation */

$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Member Relation',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Member Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-relation-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
