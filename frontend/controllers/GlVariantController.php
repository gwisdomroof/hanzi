<?php

namespace frontend\controllers;

use Yii;
use common\models\GlVariant;
use common\models\search\GlVariantSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GlVariantController implements the CRUD actions for GlVariant model.
 */
class GlVariantController extends Controller
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
     * Lists all GlVariant models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GlVariantSearch();
        $dataProvider = $searchModel->searchDup(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GlVariant model.
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
     * Creates a new GlVariant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GlVariant();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GlVariant model.
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
     * Deletes an existing GlVariant model.
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
     * Finds the GlVariant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GlVariant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GlVariant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * 高丽藏内部去重
     * @return mixed
     */
    public function actionCheckSave($id)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $id = (int)trim($id);
        $model = GlVariant::findOne($id);
        if ($model == null) {
            return '{"status":"error", "msg": "data not found."}';
        }

        if (isset(Yii::$app->request->post()['duplicate_id3'])) {
            $duplicate_id3 = Yii::$app->request->post()['duplicate_id3'];
            if (!empty($duplicate_id3)) {
                $model->duplicate_id3 = $duplicate_id3;
                if ($model->save())
                    return '{"status":"success", "dupId": "' . $model->id . '", "dupValue": "' . $model->duplicate_id3 . '"}';
            }
        }

        return '{"status":"error", "msg": "uncertain."}';
    }

}
