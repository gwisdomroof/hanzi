<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziSplit;
use common\models\HanziParts;
use common\models\HanziPartsSearch;
use common\models\HanziSet;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HanziPartsController implements the CRUD actions for HanziParts model.
 */
class HanziPartsController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all HanziParts models.
     * @return mixed
     */
    public function actionIndex()
    {
        $param = '';
        $source = '';
        $resultArray = [];
        $query = HanziParts::find()->select('part_char');

        if (isset(Yii::$app->request->post()['param'])) {
            $param = Yii::$app->request->post()['param'];
            preg_match("/(\d*)(.*)/", trim($param), $match);
            $strocks = !empty($match[1]) ? (int)$match[1] : null;
            $strockOrder = !empty($match[2]) ? $match[2] : null;

            if (!empty($strocks)) {
                $query->andWhere(['strokes' => $strocks]);
            }

            if (!empty($strockOrder)) {
                $strockOrder = str_replace('h', '1', $strockOrder);
                $strockOrder = str_replace('s', '2', $strockOrder);
                $strockOrder = str_replace('p', '3', $strockOrder);
                $strockOrder = str_replace('n', '4', $strockOrder);
                $strockOrder = str_replace('z', '5', $strockOrder);
                $query->andWhere(['~', 'stroke_order', $strockOrder]);
            }
        }

        // 来源
        $source = 6;
        if (!empty(Yii::$app->request->post()['source'])) {
            $source = (int)Yii::$app->request->post()['source'];
        }
        $sourceField = HanziParts::sourceFields()[$source];
        $query->andWhere([$sourceField => 1]);
        $query->orderBy(['strokes' => SORT_ASC, 'stroke_order' => SORT_ASC]);

        $results = $query->asArray()->all();
        foreach ($results as $r) {
            $resultArray[] = $r['part_char'];
        }

        return $this->render('index', [
            'param' => $param,
            'result' => $resultArray,
            'source' => $source,

        ]);
    }

    /**
     * 部件集中图片部件的升级
     * @return mixed
     */
    public function actionFind()
    {
        $find = !empty(Yii::$app->request->get()['find']) ? trim(Yii::$app->request->get()['find']) : '';
        $page = !empty(Yii::$app->request->get()['page']) ? (int)trim(Yii::$app->request->get()['page']) : 1;
        $size = !empty(Yii::$app->request->get()['size']) ? (int)trim(Yii::$app->request->get()['size']) : 200;
        $models = [];
        if (!empty($find)) {
            $models = HanziSplit::find()
                ->where("initial_split11 ~ '{$find}' or initial_split12 ~ '{$find}' or deform_split10 ~ '{$find}' or initial_split21 ~ '{$find}' or initial_split22 ~ '{$find}' or deform_split20 ~ '{$find}'")
                ->andWhere(['!=', 'split30_completed', 1])
                ->limit($size)
                ->offset(($page - 1) * $size)
                ->orderBy('id')
                ->all();
        }

        return $this->render('find', [
            'find' => $find,
            'page' => $page,
            'size' => $size,
            'models' => $models
        ]);

    }

    /**
     * 部件集中图片部件的升级
     * 数据库中，用split30_completed暂用，用来记录不需要被替换的记录
     * @return mixed
     */
    public function actionReplace($find, $replace)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (isset(Yii::$app->request->post()['idSet']) || isset(Yii::$app->request->post()['noNeedReplaceIdSet'])) {
            $replaceCount = 0;
            if (!empty(Yii::$app->request->post()['idSet'])) {
                $idSet = trim(Yii::$app->request->post()['idSet'], ',');
                $models = HanziSplit::find()->where("id in ({$idSet})")->orderBy('id')->all();
                foreach ($models as $model) {
                    $model->initial_split11 = str_replace(trim($find), trim($replace), $model->initial_split11);
                    $model->initial_split12 = str_replace(trim($find), trim($replace), $model->initial_split12);
                    $model->deform_split10 = str_replace(trim($find), trim($replace), $model->deform_split10);
                    $model->initial_split21 = str_replace(trim($find), trim($replace), $model->initial_split21);
                    $model->initial_split22 = str_replace(trim($find), trim($replace), $model->initial_split22);
                    $model->deform_split20 = str_replace(trim($find), trim($replace), $model->deform_split20);
                    if (!$model->save()) {
                        return '{"status":"failed", "msg": "id {' . $model->id . '} save failed."}';
                    }
                }
                $replaceCount = count($models);
            }

            $noNeedReplaceCount = 0;
            if (!empty(Yii::$app->request->post()['noNeedReplaceIdSet'])) {
                $noNeedReplaceIdSet = trim(Yii::$app->request->post()['noNeedReplaceIdSet'], ',');
                $noNeedReplaceCount = HanziSplit::updateAll(['split30_completed' => 1], "id in ({$noNeedReplaceIdSet})");
            }

            return '{"status":"success", "replaceCount": ' . $replaceCount . ', "noNeedReplaceCount":' . $noNeedReplaceCount . '}';

        } else {
            return '{"status":"failed", "msg": "idSet is blank."}';
        }

    }

    /**
     * 部件集中图片部件的升级
     * 数据库中，用split30_completed暂用，用来记录不需要被替换的记录
     * @return mixed
     */
    public function actionClearFlag()
    {
        HanziSplit::updateAll(['split30_completed' => 0]);
        $this->redirect('find');
    }

    private function unicode_decode_bak($uStr)
    {
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $uStr, $matches);
        if (!empty($matches)) {
            $uStr = '';
            for ($j = 0; $j < count($matches[0]); $j++) {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0) {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code) . chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $uStr .= $c;
                } else {
                    $uStr .= $str;
                }
            }
        }
        return $uStr;
    }

    function unicode_decode($name)
    {
        $json = '{"str":"' . $name . '"}';
        $arr = json_decode($json, true);
        if (empty($arr)) return '';
        return $arr['str'];
    }

    /**
     * @return mixed
     */
    public function actionFont()
    {
        $fontOne = [];

        $myfile = fopen("e:\\Code\\tools\\text\\zhzkunicode.txt", "r") or die("Unable to open file!");
        while (!feof($myfile)) {
            $fontOne[] = fgets($myfile);
        }
        fclose($myfile);

        return $this->render('font', [
            'fontOne' => $fontOne,
        ]);
    }

    /**
     * Lists all HanziParts models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new HanziPartsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['id' => SORT_ASC]]);
        $dataProvider->pagination->pageSize = 50;

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all HanziParts models.
     * @return mixed
     */
    public function actionFrequency()
    {
        $sqls = [];
        $index = 1;
        $models = HanziParts::find()->orderBy('id')->all();
        foreach ($models as $model) {
            $parts = $model->part_char;
            $cnt1 = HanziSplit::find()->where("initial_split11 ~ '{$parts}' or initial_split12 ~ '{$parts}' or initial_split21 ~ '{$parts}' or initial_split22 ~ '{$parts}'")->count();
            $cnt2 = HanziSet::find()->where("max_split ~ '{$parts}'")->count();
            $cnt = $cnt1 + $cnt2;

            $sqls[] = "update hanzi_parts set frequency=$cnt where id = $model->id;";

//            if ($index++ > 100)
//                break;
        }
        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\set-hanzi-parts-frequency.txt', $contents);

        echo "success!";
        die;
    }

    /**
     * Lists all HanziParts models.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new HanziParts();

        $result = [];
        if ($model->load(Yii::$app->request->post())) {
            $result = $model->batchSave();
        }
        return $this->render('add', [
            'model' => $model,
            'result' => $result
        ]);
    }

    /**
     * Lists all HanziParts models.
     * @return mixed
     */
    public function actionRegister()
    {
        $model = new HanziParts();

        $result = [];
        if ($model->load(Yii::$app->request->post())) {
            $result = $model->batchRegister();
        }
        return $this->render('register', [
            'model' => $model,
            'result' => $result
        ]);
    }

    /**
     * Lists all HanziParts models.
     * @return mixed
     */
    public function actionCheck($id, $field)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $id = (int)trim($id);
        $model = HanziParts::findOne($id);
        if ($model == null) {
            return '{"status":"error", "msg": "data not found."}';
        }

        if (isset(Yii::$app->request->post()['value'])) {
            $value = Yii::$app->request->post()['value'];
            $field = '' . trim($field);
            if (strcmp($field, "lqhz") == 0) {
                $model->src_lqhanzi = (int)trim($value);
            } elseif (strcmp($field, "shp") == 0) {
                $model->is_search_part = (int)trim($value);
            } elseif (strcmp($field, "stp") == 0) {
                $model->is_split_part = (int)trim($value);
            } elseif (strcmp($field, "rp") == 0) {
                $model->replace_parts = trim($value);
            }

            if ($model->save())
                return '{"status":"success", "id": "' . $model->id . '"}';
            else
                var_dump($model->getErrors());
        }

        return '{"status":"error", "msg": "uncertain."}';
    }

    /**
     * Displays a single HanziParts model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new HanziParts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HanziParts();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HanziParts model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing HanziParts model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['admin']);
    }

    /**
     * Finds the HanziParts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HanziParts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HanziParts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
