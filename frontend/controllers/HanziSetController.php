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
