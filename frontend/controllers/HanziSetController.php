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



    /**
     * import from xls file.
     * @return mixed
     */
    public function actionSearch()
    {
        $hanziSearch = new HanziSetSearch();

        # 如果是post请求，则转为Get请求
        if (Yii::$app->request->post()) {
            $hanziSearch->load(Yii::$app->request->post());
            $this->redirect(['search', 'HanziSetSearch[param]' => $hanziSearch->param]);
        }
        
        $data = array();
        $pagination = new \yii\data\Pagination(['totalCount' => 0]);
        $message = null;

        if ($hanziSearch->load(Yii::$app->request->get()) && $hanziSearch->validate()) {

            $hanzi = $hanziSearch->regSearch($hanziSearch->param);

            $count = $hanzi->count();

            $message = $count == 0 ? "查询结果为空。" : "共检索到".$count."条数据。";

            $pagination = new \yii\data\Pagination(['totalCount' => $count, 'pageSize' => 100]);

            $data = $hanzi->orderBy('id')->offset($pagination->offset)->limit(100)->all();
        }

        return $this->render('search', [
            'hanziSearch' => $hanziSearch,
            'data' => $data,
            'pagination' => $pagination,
            'message' => $message,
        ]);
    }

}
