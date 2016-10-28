<?php

namespace frontend\controllers;

use Yii;
use common\models\GlVariant;
use common\models\search\GlVariantSearch;
use yii\base\InvalidValueException;
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
     * Expert chooses which duplication is correct. 0 for both wrong.
     * @param $id int
     * @param $choice int
     */
    public function actionJudge($id, $choice) {
        $model = $this->findModel($id);
        switch($choice) {
            case 0:
                $model->duplicate_id3 = "0";
                break;
            case 1:
                $model->duplicate_id3 = $model->duplicate_id1;
                break;
            case 2:
                $model->duplicate_id3 = $model->duplicate_id2;
                break;
            default:
                throw new InvalidValueException("$choice out of range");
        }

        if ($model->save()) {
            return json_encode(["code"=>0]);
        }
        return json_encode(["code"=>1, "msg"=>$model->getErrors()]);
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
}
