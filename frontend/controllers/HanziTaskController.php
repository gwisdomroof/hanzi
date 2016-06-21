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
     * 列出当前任务。
     * @type 任务类型
     * @return mixed
     */
    public function actionIndex($type=1)
    {
        $searchModel = new HanziTaskSearch();

        $param = Yii::$app->request->queryParams;
        unset($param['type']);

        if (!isset(Yii::$app->request->queryParams['HanziTaskSearch']['member.username'])) {
            $param = array_merge($param, [
                'HanziTaskSearch' => [
                    'member.username' => Yii::$app->user->identity->username
                    ]
                ]);
        }

        $param['HanziTaskSearch']['task_type'] = $type;

        $dataProvider = $searchModel->search($param);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => $type
        ]);

    }

    /**
     * Lists all HanziTask models.
     * @return mixed
     */
    public function actionAdmin($type=1)
    {
        $searchModel = new HanziTaskSearch();

        $param = Yii::$app->request->queryParams;
        unset($param['type']);

        if (!isset(Yii::$app->request->queryParams['HanziTaskSearch']['leader.username'])) {
            $param = array_merge($param, [
                'HanziTaskSearch' => [
                    'leader.username' => Yii::$app->user->identity->username
                    ]
                ]);
        }

        $param['HanziTaskSearch']['task_type'] = $type;

        $dataProvider = $searchModel->search($param);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => $type
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
    public function actionCreate($type=1)
    {
        $model = new HanziTask();

        // set current seq
        $model->seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
        $model->leader_id = Yii::$app->user->id;
        $model->task_type = $type;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 向系统组长申请任务
     * 页码自动分配
     * @return mixed
     */
    public function actionApply($type=1)
    {
        $model = new HanziTask();

        // 如果有工作状态为“初分配”“进行中”或为空，则不能继续申请
        if (HanziTask::checkApplyPermission(Yii::$app->user->id, $type)) {
            throw new HttpException(403, '您有工作未完成，请先完成手头的工作。'); 
        }

        // set default value
        $systemLeader = HanziTask::getSystemLeader();
        $model->leader_id = $systemLeader['id'];
        $model->user_id = Yii::$app->user->id;
        $model->task_type = $type;

        // set current seq
        $model->seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'type' => $model->task_type]);
        } else {
            return $this->render('apply', [
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
        if ($model->leader_id !== $userId && $model->user_id !== $userId) {
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

        return $this->redirect(['admin']);
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
