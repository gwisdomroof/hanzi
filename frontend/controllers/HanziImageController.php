<?php

namespace frontend\controllers;

use common\models\HanziImage;
use Yii;
use yii\web\Controller;


class HanziImageController extends \yii\web\Controller
{
    public function actionGet($fn)
    {
    	$model = HanziImage::find()->where(['name' => $fn])->one();

        return empty($model) ? '' : $model->value;

    }

}
