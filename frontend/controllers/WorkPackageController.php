<?php

namespace frontend\controllers;

use common\models\WorkClock;
use Yii;
use common\models\WorkPackage;
use common\models\search\WorkPackageSearch;
use common\models\HanziTask;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WorkPackageController implements the CRUD actions for WorkPackage model.
 */
class WorkPackageController extends Controller
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
     * Lists all WorkPackage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WorkPackageSearch();
        $searchModel->userid = Yii::$app->user->id;
        $dataProvider = $searchModel->search(['progress' => 'ongoing']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all WorkPackage models.
     * @return mixed
     */
    public function actionFinished()
    {
        $searchModel = new WorkPackageSearch();
        $searchModel->userid = Yii::$app->user->id;
        $dataProvider = $searchModel->search(['progress' => 'finished']);

        return $this->render('finished', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all WorkPackage models.
     * @return mixed
     */
    public function actionDetail()
    {
        $searchModel = new \common\models\search\HanziTaskSearch();
        $searchModel->user_id = Yii::$app->user->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('detail', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Lists all WorkPackage models.
     * @param type
     * type = 1，表示当日工作已完成，请去打卡
     * type = 2，表示当前工作包已完成，请重新领任务
     * @return mixed
     */
    public function actionInfo($type, $stage)
    {
        $url = null;
        $msg = null;
        $typeInfo = WorkPackage::types()["{$type}"];
        if ($stage == 2) {
            $msg = "当日{$typeInfo}工作已完成。";
            if (!WorkClock::ifClockedToday(Yii::$app->user->id, $type)) {
                $url = "/work-clock/create?type={$type}";
                $msg = "当日{$typeInfo}工作已完成，请您去<a href='$url'>打卡</a>。";
            }

        } elseif ($stage == 1) {
            $url = "/work-package/create?type={$type}";
            $msg = "当前{$typeInfo}工作包已完成，请您重新<a href='$url'>领取任务</a>。";
        }
        return $this->render('info', [
            'url' => $url,
            'msg' => $msg
        ]);
    }

    /**
     * Lists all WorkPackage models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new WorkPackageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WorkPackage model.
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
     * Creates a new WorkPackage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WorkPackage();
        $type = Yii::$app->request->get('type');
        if (!empty($type)) {
            $model->type = $type;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 更新session
            if ($model->type == HanziTask::TYPE_SPLIT) {
                Yii::$app->session->set('curSplitPackage', $model->attributes);
            } elseif ($model->type == HanziTask::TYPE_INPUT) {
                Yii::$app->session->set('curRecognizePackage', $model->attributes);
            } elseif ($model->type == HanziTask::TYPE_DEDUP) {
                Yii::$app->session->set('curDedupPackage', $model->attributes);
            } elseif ($model->type == HanziTask::TYPE_GAOLI_SPLIT) {
                Yii::$app->session->set('curGaoliSplitPackage', $model->attributes);
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WorkPackage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 更新session
            if ($model->type == HanziTask::TYPE_SPLIT) {
                Yii::$app->session->set('curSplitPackage', $model->attributes);
            } elseif ($model->type == HanziTask::TYPE_INPUT) {
                Yii::$app->session->set('curRecognizePackage', $model->attributes);
            } elseif ($model->type == HanziTask::TYPE_DEDUP) {
                Yii::$app->session->set('curDedupPackage', $model->attributes);
            } elseif ($model->type == HanziTask::TYPE_GAOLI_SPLIT) {
                Yii::$app->session->set('curGaoliSplitPackage', $model->attributes);
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WorkPackage model.
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
     * Finds the WorkPackage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return WorkPackage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WorkPackage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
