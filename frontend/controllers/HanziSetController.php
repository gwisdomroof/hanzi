<?php

namespace frontend\controllers;

use common\models\HanziSet;
use common\models\search\HanziSetSearch;
use common\models\LqVariant;
use common\models\search\LqVariantSetSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HanziController implements the CRUD actions for HanziSet model.
 */
class HanziSetController extends Controller
{

    private $hanziIds = [];

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
     * 部件笔画检字法
     * @return mixed
     */
    public function actionSearch()
    {
        # 将post请求转为Get请求，以免get请求中的参数累加
        if (Yii::$app->request->post()) {
            $this->redirect(['search', 'param' => Yii::$app->request->post()['HanziSetSearch']['param']]);
        }

        $data = [];
        $pagination = new \yii\data\Pagination(['totalCount' => 0]);
        $message = null;
        $hanziSearch = new HanziSetSearch();
        if (isset(Yii::$app->request->get()['param'])) {
            $hanziSearch->param = trim(Yii::$app->request->get()['param']);
        }

        $mode = 'wsearch';
        if (!empty($hanziSearch->param) && $hanziSearch->validate()) {
            if (preg_match("/^!/", $hanziSearch->param)) {    # 反查汉字拆分序列
                $data = $hanziSearch->rsearch();
                $mode = 'rsearch';
            } else {    # 部件笔画检字
                $query = $hanziSearch->wsearch();
                $count = $query->count();
                $message = $count == 0 ? "查询结果为空。" : "共检索到" . $count . "条数据。";
                $pagination = new \yii\data\Pagination(['totalCount' => $count, 'pageSize' => 100]);
                $data = $query->orderBy('id')->offset($pagination->offset)->limit(100)->all();
            }
        }

        return $this->render('search', [
            'hanziSearch' => $hanziSearch,
            'mode' => $mode,
            'data' => $data,
            'pagination' => $pagination,
            'message' => $message,
        ]);
    }


    /**
     * @param $str
     * 依次提取参数中的汉字，返回数组
     */
    private function extractHanzi($str)
    {
        // 删除空格、结构符、圈数字、分号等
        $str = mb_ereg_replace('[\s\?&{}\[\]0-9a-zA-Z①-⑳⿰-⿻；？󰃾󰃿]', '', $str);
        $str = mb_ereg_replace('<.>', '', $str);
        $str = mb_ereg_replace('（.）', '', $str);
        // 数组
        if (empty($str)) {
            return [];
        } else {
            $return = array_unique(preg_split('/(?<!^)(?!$)/u', $str));
            setlocale(LC_COLLATE, 'sk_SK.utf8');
            $f = function ($a, $b) {
                return strcoll($a, $b);
            };

            usort($return, $f);
            return $return;
        }
    }

    /*
     * 处理每一个单独的min_split_ids，得到按照unicode内码排序后的mix_split_ids
     */
    private function generateMixIds($ids)
    {
        if (mb_strlen($ids, 'utf-8') == 1) {
            if (empty($this->hanziIds[$ids])) {
                return $ids;
            } else {
                return "<{$ids}>" . $this->generateMixIds($this->hanziIds[$ids]);
            }
        } else {
            $items = preg_split('/(?<!^)(?!$)/u', $ids);
            $totalIds = '';
            foreach ($items as $item) {
                $totalIds .= $this->generateMixIds($item);
            }
            return $totalIds;
        }

    }

    /**
     * 根据初步拆分递归混合拆分、最大拆分、拆分序列、结构等信息，生成sql文.
     * @return mixed
     */
    public function actionGenerateSearch()
    {
        mb_internal_encoding("UTF-8");
        mb_regex_encoding("UTF-8");

        $this->hanziIds = require_once('unicodeMinSplitIds.php');

        $index = 1;
        $output = [];
//        $todoList = require_once('todo.php');
//        foreach ($todoList as $word => $minIds) {
        foreach ($this->hanziIds as $word => $minIds) {
            $idsArr = explode("；", $minIds);
            $resultArr = [];
            foreach ($idsArr as $ids) {
                $eachMixIds = $this->generateMixIds($ids);
                $resultArr[] = str_replace('；', '&', $eachMixIds);
            }
            $mixIds = implode("；", $resultArr);
            $maxIds = mb_ereg_replace('<.>', '', $mixIds);
            $strokeSerial = implode('', $this->extractHanzi($mixIds));

            $output[] = "{$word}\t{$mixIds}\t{$maxIds}\t{$strokeSerial}";
//            echo "{$word}\t{$mixIds}\t{$maxIds}\t{$strokeSerial}<br/>";
            if (++$index > 5000) {
                $contents = implode("\r\n", $output)."\r\n";
                file_put_contents('d:\Inbox\unicode-mix-max-ids.txt', $contents, FILE_APPEND);
                unset($output);
                $index = 1;
            }
        }

        $contents = implode("\r\n", $output);
        file_put_contents('d:\Inbox\unicode-mix-max-ids.txt', $contents, FILE_APPEND);

        echo "success!";
        die;
    }


    /**
     * Lists all HanziSet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HanziSetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HanziSet model.
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
     * Creates a new HanziSet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HanziSet();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HanziSet model.
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
     * Deletes an existing HanziSet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the HanziSet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HanziSet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HanziSet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
