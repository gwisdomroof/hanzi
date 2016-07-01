<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziUserTask;
use common\models\HanziUserTaskSearch;
use common\models\HanziTask;
use common\models\HanziHyyt;
use common\models\Hanzi;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserTaskController implements the CRUD actions for HanziUserTask model.
 */
class UserTaskController extends Controller
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
     * Lists all HanziUserTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HanziUserTaskSearch();

        $param = Yii::$app->request->queryParams;
        if (!isset(Yii::$app->request->queryParams['HanziUserTaskSearch']['userid'])) {
            $param['HanziUserTaskSearch']['userid'] = Yii::$app->user->id;
        }

        $splitNum = HanziUserTask::find()->where(['userid'=>Yii::$app->user->id, 'task_type'=>HanziUserTask::TYPE_SPLIT])->count();
        $inputNum = HanziUserTask::find()->where(['userid'=>Yii::$app->user->id, 'task_type'=>HanziUserTask::TYPE_INPUT])->count();

        $dataProvider = $searchModel->search($param);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'splitNum' => $splitNum,
            'inputNum' => $inputNum,
        ]);
    }

    /**
     * 扫描任务表，计算已完成任务.
     * @return mixed
     */
    public function actionScan()
    {
        // 获取用户的任务列表
        $tasks = HanziTask::find()->orderBy('page')->all();
        foreach ($tasks as $task) {
            if ($task->task_type == HanziTask::TYPE_SPLIT) {
                $pageSize = Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false);
                $page = $task->page;
                $models = Hanzi::find()->orderBy('id')->where(['duplicate' => 0])->offset($pageSize * ($page - 1))->limit($pageSize)->all();

                $seq = $task->seq;
                foreach ($models as $model) {
                    if (!$model->isNew($seq)) {
                        HanziUserTask::addItem($task->user_id, $model->id, $task->task_type, $task->task_seq, false);
                    }
                }

            } elseif ($task->task_type == HanziTask::TYPE_INPUT) {
                $page = $task->page;
                $models = HanziHyyt::find()->orderBy('id')->where(['page' => $page])->all();
                $seq = $task->seq;
                foreach ($models as $model) {
                    if (!$model->isNew($seq)) {
                        HanziUserTask::addItem($task->user_id, $model->id, $task->task_type, $task->task_seq, false);
                    }
                }
            }

            echo 'user id: ' . $task->user_id . '; page: ' . $task->page . '.<br/>';
        }
        echo 'success!';
    }



    /**
     * Displays a single HanziUserTask model.
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
     * Creates a new HanziUserTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HanziUserTask();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HanziUserTask model.
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
     * Deletes an existing HanziUserTask model.
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
     * Finds the HanziUserTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HanziUserTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HanziUserTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
