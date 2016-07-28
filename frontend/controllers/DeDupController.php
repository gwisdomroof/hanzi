<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziSet;
use common\models\HanziSetSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PHPExcel;
use PHPExcel\IOFactory;

/**
 * HanziController implements the CRUD actions for HanziSet model.
 */
class DeDupController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 高丽藏内部，按照郑码去重
     * @return mixed
     */
    public function actionGaoli()
    {
        $models = HanziSet::find()->orderBy('id')->where(['source'=>HanziSet::SOURCE_GAOLI])->all();
    }
}