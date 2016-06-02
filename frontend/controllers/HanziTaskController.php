<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziTask;
use common\models\HanziTaskSearch;
use common\models\User;
use common\models\Hanzi;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * HanziTaskController implements the CRUD actions for HanziTask model.
 */
class HanziTaskController extends Controller
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
     * Lists all HanziTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HanziTaskSearch();

        $param = Yii::$app->request->queryParams;

        if (!isset(Yii::$app->request->queryParams['HanziTaskSearch']['member.username'])) {
            $param = array_merge($param, [
                'HanziTaskSearch' => [
                    'member.username' => Yii::$app->user->identity->username
                    ]
                ]);
        }

        $dataProvider = $searchModel->search($param);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }


    /**
     * Displays a single HanziTask model.
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
     * Creates a new HanziTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HanziTask();

        // set default seq
        $model->seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            // var_dump($model->getErrors()); die;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HanziTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model =  $this->findModel($id);

        $userId = Yii::$app->user->id;
        if ($model->leader_id !== $userId) {
            throw new HttpException(401, '对不起，您无权修改。'); 
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing HanziTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        
        $model =  $this->findModel($id);

        $userId = Yii::$app->user->id;
        if ($model->leader_id !== $userId) {
            throw new HttpException(401, '对不起，您无权删除。'); 
        }

        $model->delete();

        return $this->redirect(['index']);
    }


    /**
     * Finds the HanziTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HanziTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HanziTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
        
}
