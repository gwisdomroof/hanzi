<?php

namespace frontend\controllers;

use common\models\HanziSet;
use common\models\search\HanziSetSearch;
use common\models\LqVariant;
use common\models\HanziSplit;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HanziController implements the CRUD actions for HanziSet model.
 */
class UtilController extends Controller
{

    /**
     * @param $str
     */
    public function actionCompare()
    {
        $dups = require_once('_compare.php');
        $dups1 = explode(',', $dups[3]);
        $dups2 = explode(',', $dups[4]);
        $diff = array_diff($dups1, $dups2);
        $same = array_intersect($dups1, $dups2);

        echo "=====diff =====<br/>";
        foreach ($diff as $i) {
            echo "{$i}<br/>";
        }

        echo "=====same=====<br/>";
        foreach ($same as $i) {
            echo "{$i}<br/>";
        }

        die;
    }
}