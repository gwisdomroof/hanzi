<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\HanziTask;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = '任务列表';
?>

<div class="col-sm-10 hanzi-task-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'id',
                "headerOptions" => ["width" => "30"]
            ],
            [
                'attribute' => 'task_type',
                'value' => function ($data) {
                    return empty($data['task_type']) ? '' : $data->types()[$data['task_type']];
                },
                'filter' => HanziTask::types()
            ],
            'leader.username',
            [
                'attribute' => 'page',
                'value' => function ($data) {
                    if ($data->task_type == HanziTask::TYPE_SPLIT) {
                        $url = 'hanzi-split/index';
                        return empty($data['page']) ? '' : Html::a($data['page'], yii\helpers\Url::to([$url, 'page' => $data->page], true));
                    } elseif ($data->task_type == HanziTask::TYPE_GAOLI_SPLIT) {
                        $url = 'hanzi-split/index';
                        return empty($data['page']) ? '' : Html::a($data['page'], yii\helpers\Url::to([$url, 'page' => $data->page], true));
                    } elseif($data->task_type == HanziTask::TYPE_INPUT) {
                        $url ='hanzi-hyyt/index';
                        return empty($data['page']) ? '' : Html::a($data['page'], yii\helpers\Url::to([$url, 'page' => $data->page], true));
                    } elseif($data->task_type == HanziTask::TYPE_DEDUP) {
                        $url ='gltw-dedup/dedup';
                        return empty($data['page']) ? '' : Html::a($data['page'], yii\helpers\Url::to([$url, 'id' => $data->page], true));
                    }

                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return !isset($data['status']) ? '' : $data->statuses()[$data['status']];
                },
                'filter' => HanziTask::statuses(),
                "headerOptions" => ["width" => "120"]
            ],
        ],
    ]); ?>
</div>

<div class="col-sm-2 pull-right">
    <nav class="navbar" style="background-color: #FFF8DC; border: 1px solid #E0DCBF;">
        <ul class="nav">
            <li><a href="<?= Url::toRoute(['index']) ?>">进行中</a></li>
            <li><a href="<?= Url::toRoute(['finished']) ?>">已完成</a></li>
            <li><a href="<?= Url::toRoute(['create']) ?>">领任务</a></li>
            <li style="background-color:#f5f5f5;"><a href="<?= Url::toRoute(['detail']) ?>">详　情</a></li>
        </ul>
    </nav>
</div>
