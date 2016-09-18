<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziHyyt;
use common\models\HanziTask;
use common\models\HanziUserTask;
use common\models\search\HanziHyytSearch;
use common\models\WorkPackage;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HanziHyytController implements the CRUD actions for HanziHyyt model.
 */
class HanziHyytController extends Controller
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


    private function recognizeInitial()
    {
        $userId = Yii::$app->user->id;
        // 检查并设置当前任务包的session值
        $curRecognizePackage = Yii::$app->session->get('curRecognizePackage');
        if (!isset($curRecognizePackage) || empty($curRecognizePackage['id'])) {
            $curRecognizePackage = WorkPackage::find()
                ->where(['userid' => $userId, 'type' => HanziTask::TYPE_INPUT])
                ->andWhere('progress < volume')
                ->orderBy('created_at')
                ->one();
            if (!empty($curRecognizePackage))
                Yii::$app->session->set('curRecognizePackage', $curRecognizePackage->attributes);
        }

        // 检查当前工作包是否完成
        $finishedCount = (int)HanziUserTask::getFinishedWorkCountFrom($userId, HanziTask::TYPE_INPUT, $curRecognizePackage['created_at']);
        if ($finishedCount >= (int)$curRecognizePackage['volume']) {
            // 当前工作包已完成，设置进度
            WorkPackage::updateProgress($curRecognizePackage['id'], $finishedCount);
            // 检查用户页面池，如果有未完成的页面，设置为continue
            HanziTask::setUnfinishedHyytPage($userId);
            // 跳转任务页面
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_INPUT, 'stage' => 1]);
            return false;
        }

        // 检查当日工作是否已完成
        $finishedCountToday = (int)HanziUserTask::getFinishedWorkCountToday($userId, HanziTask::TYPE_INPUT);
        if ($finishedCountToday >= (int)$curRecognizePackage['daily_schedule']) {
            // 当日工作已完成，跳转打卡页面
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_INPUT, 'stage' => 2]);
            return false;
        }

        // 设置当前工作进度
        Yii::$app->session->set('curRecognizeProgress', "{$finishedCountToday}/{$curRecognizePackage['daily_schedule']}");

        // 检查并设置当前工作页面的session值。
        $curPage = Yii::$app->session->get('curRecognizePage');
        if (!isset($curPage) || empty($curPage['id'])) {
            // 寻找页面池中page值最小、状态为“初分配”“进行中”的页面，如果没有，则申请新页
            $curPage = HanziTask::getUnfinishedMinPage($userId, HanziTask::TYPE_INPUT);
            if (empty($curPage)) {
                $curPage = HanziTask::getNewPage($userId, HanziTask::TYPE_INPUT);
            }
            Yii::$app->session->set('curRecognizePage', $curPage->attributes);
        }
        return true;
    }

    /**
     * 查看或修改指定的页面.
     * @param page 页面
     * @param seq 阶段
     * @return mixed
     */
    public function actionRecognize()
    {
        // 执行初始化检查
        if ($this->recognizeInitial()) {
            // 跳转工作页面
            $curPage = Yii::$app->session->get('curRecognizePage');
            $this->redirect(['index', 'page' => $curPage['page']]);
        };
    }

    /**
     * 查看或修改指定的页面， 修改包括两种权限：一、页面权限；二、数据权限。
     * 1、如果两种权限都没有，则只能阅读不能修改；
     * 2、如果有页面权限，但是其中有些数据没有权限，则没有权限的数据只能阅读，有权限的数据可以修改。
     * 有页面权限时的特殊情况：
     * 1、如果页面状态为continue，则表示该页面自己完成了一部分，然后提交给了系统。因此，只能修改自己已完成的页面；
     * 2、如果页面状态不为continue，而该页面中有其他人完成的数据，则对这部分数据只能阅读，不能修改。
     * @param page 页面
     * @param seq 阶段
     * @return mixed
     */
    public function actionIndex($page = -1, $seq = 1)
    {
        $searchModel = new HanziHyytSearch();
        $param['HanziHyytSearch']['page'] = (int)$page;
        $models = $searchModel->search($param)->getModels();

        $userId = Yii::$app->user->id;
        $task = HanziTask::getTaskByPage($userId, $page, HanziTask::TYPE_INPUT);
        $writeSeq = empty($task) ? '' : $task['seq'];
        $writeable = $writeSeq == $seq; # 页面权限
        $view = $seq == 3 ? 'determine' : 'input';
        # 当前页面中已经完成的数据
        $finished = HanziTask::getFinishedTasksByOthers($userId, $page, HanziTask::TYPE_INPUT);

        return $this->render($view, [
            'models' => $models,
            'finished' => $finished,
            'pageStatus' => $task['status'],
            'writeable' => $writeable,
            'curPage' => $page,
            'seq' => $seq
        ]);
    }

    /**
     * Lists all HanziHyyt models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new HanziHyytSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HanziHyyt model.
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
     * Creates a new HanziHyyt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HanziHyyt();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HanziHyyt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['index', 'page' => $model->page]);
    }

    /**
     * Updates an existing HanziHyyt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionModify($id, $seq = 1)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        // 执行初始化检查
        if ($this->recognizeInitial()) {
            $userId = Yii::$app->user->id;
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $addScore = 0;
                if (!$model->isNew($seq)) {
                    if (HanziUserTask::addItem($userId, $model->id, HanziTask::TYPE_INPUT, 1, $seq)) {
                        $addScore = 1;  #每增一项加一分
                    }
                }
                // 检查当前汉语大字典页面是否完成
                $curPage = Yii::$app->session->get('curRecognizePage');
                if (isset($curPage) && HanziTask::checkFinished($curPage['id'], $curPage['page'])) {
                    unset(Yii::$app->session['curRecognizePage']);
                }
                $this->recognizeInitial();
                return '{"status":"success", "id": ' . $id . ', "score": ' . $addScore . '}';
            } else {
                return '{"status":"error", "id": ' . $id . '}';
            }
        }
    }

    /**
     * Deletes an existing HanziHyyt model.
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
     * Finds the HanziHyyt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HanziHyyt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HanziHyyt::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
