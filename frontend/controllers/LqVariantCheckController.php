<?php

namespace frontend\controllers;

use common\models\LqVariantCheck;
use common\models\search\LqVariantCheckSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LqVariantCheckController implements the CRUD actions for LqVariantCheck model.
 */
class LqVariantCheckController extends Controller
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
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LqVariantCheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionAdmin()
    {
        # 将post请求转为Get请求，以免get请求中的参数累加
        if (Yii::$app->request->post()) {
            $level2 = Yii::$app->request->post()['LqVariantCheckSearch']['level2'];
            $bconfirm = Yii::$app->request->post()['LqVariantCheckSearch']['bconfirm'];
            $this->redirect(['admin', "LqVariantCheckSearch[level2]" =>$level2, "LqVariantCheckSearch[bconfirm]" => $bconfirm]);
        }

        $searchModel = new LqVariantCheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionPages()
    {
        return $this->render('pages');
    }
   /**
     * @param string $id
     * @return mixed
     */
    public function actionModify($id)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return '{"status":"success", "id": ' . $id. '}';
        } else {
            return '{"status":"error", "id": ' . $id. '}';
        }
    }

    /**
     * Creates a new LqVariantCheck model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LqVariantCheck();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LqVariantCheck model.
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
     * Deletes an existing LqVariantCheck model.
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
     * Finds the LqVariantCheck model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LqVariantCheck the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LqVariantCheck::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
