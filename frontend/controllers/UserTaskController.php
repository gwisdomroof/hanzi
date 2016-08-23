<?php

namespace frontend\controllers;

use common\models\HanziUserTask;
use common\models\search\HanziUserTaskSearch;
use common\models\HanziTask;
use common\models\HanziHyyt;
use common\models\Hanzi;
use common\models\user;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
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
    public function actionOrder()
    {
        
        $searchModel = new HanziUserTaskSearch();

        $param = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->countScores($param);

        // 使关联列的排序生效
        $dataProvider->sort->attributes['cnt'] = [
            'asc' => ['cnt' => SORT_ASC],
            'desc' => ['cnt' => SORT_DESC],
        ];

        // 使关联列的排序生效
        $dataProvider->sort->attributes['user.username'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];

        return $this->render('order', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all HanziUserTask models.
     * @return mixed
     */
    public function actionAdmin($type = HanziUserTask::TYPE_COLLATE)
    {
        $searchModel = new HanziUserTaskSearch();

        $param = Yii::$app->request->queryParams;

        $param['HanziUserTaskSearch']['task_type'] = $type;

        if ((int)$type  !== HanziUserTask::TYPE_COLLATE  && (int)$type  !== HanziUserTask::TYPE_DOWNLOAD && (int)$type  !== HanziUserTask::TYPE_INPUT) {
           throw new HttpException('400', '参数有误：type');
        }

        $dataProvider = $searchModel->search($param);

        $members =  user::find()
            ->select(['username as value', 'username as label','id'])
            ->where(['status' => user::STATUS_ACTIVE])
            ->asArray()
            ->all();

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => $type,
            'members' => $members,
        ]);
    }

    /**
     * Lists all HanziUserTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userid = Yii::$app->user->id;
        $searchModel = new HanziUserTaskSearch();

        $param = Yii::$app->request->queryParams;
        if (!isset(Yii::$app->request->queryParams['HanziUserTaskSearch']['userid'])) {
            $param['HanziUserTaskSearch']['userid'] = Yii::$app->user->id;
        }

        $userid = Yii::$app->user->id;
        $groupScore = HanziUserTask::find()->select('task_type, sum(quality) as score')->where(['userid' => $userid])->groupBy(['task_type'])->asArray()->all();

        $dataProvider = $searchModel->search($param);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'groupScore' => $groupScore,
        ]);
    }

    /**
     * 扫描任务表，计算已完成任务.
     * @return mixed
     */
    public function actionScan($min, $max)
    {
        // 获取用户的任务列表
        $tasks = HanziTask::find()->orderBy('page')->andWhere(['>=', 'page', $min])->andWhere(['<=', 'page', $max])->all();
        foreach ($tasks as $task) {
            if ($task->task_type == HanziTask::TYPE_SPLIT) {
                $pageSize = Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false);
                $page = $task->page;
                $models = Hanzi::find()->orderBy('id')->where(['duplicate' => 0])->offset($pageSize * ($page - 1))->limit($pageSize)->all();

                $seq = $task->seq;
                foreach ($models as $model) {
                    if (!$model->isNew($seq)) {
                        HanziUserTask::addItem($task->user_id, $model->id, $task->task_type, $task->seq);
                    }
                }

            } elseif ($task->task_type == HanziTask::TYPE_INPUT) {
                $page = $task->page;
                $models = HanziHyyt::find()->orderBy('id')->where(['page' => $page])->all();
                $seq = $task->seq;
                foreach ($models as $model) {
                    if (!$model->isNew($seq)) {
                        HanziUserTask::addItem($task->user_id, $model->id, $task->task_type, $task->seq);
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

        $members =  user::find()
            ->select(['username as value', 'username as label','id'])
            ->where(['status' => user::STATUS_ACTIVE])
            ->asArray()
            ->all();

        if ($model->load(Yii::$app->request->post())) {
            // 设置默认值
            if (empty($model->task_seq)) {
                $model->task_seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
            }

            if ($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->task_type = Yii::$app->request->get('type');
        return $this->render('create', [
            'model' => $model,
            'members' => $members,
        ]);

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

        $members =  user::find()
            ->select(['username as value', 'username as label','id'])
            ->where(['status' => user::STATUS_ACTIVE])
            ->asArray()
            ->all();

        if ($model->load(Yii::$app->request->post())) {
            // 设置默认值
            if (empty($model->task_seq)) {
                $model->task_seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
            }

            if ($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        } 
        return $this->render('update', [
            'model' => $model,
            'members' => $members,
        ]);
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
