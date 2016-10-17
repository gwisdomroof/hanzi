<?php

namespace frontend\controllers;

use common\models\HanziSplit;
use common\models\search\HanziSplitSearch;
use common\models\HanziTask;
use common\models\HanziUserTask;
use common\models\User;
use common\models\WorkPackage;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * HanziController implements the CRUD actions for HanziSplit model.
 */
class HanziSplitController extends Controller
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
     * Lists all HanziSplit models.
     * @return mixed
     */
    public function actionStage()
    {
        $msg = null;
        if (!empty(Yii::$app->request->post())) {
            $stage = Yii::$app->request->post()['stage'];
            if (!empty(Yii::$app->request->post()['stage'])) {
                Yii::$app->get('keyStorage')->set('frontend.current-split-stage', 2);
            }
            $msg = '已启动，系统进入回查阶段！';
        }
        return $this->render('stage', [
            'msg' => $msg
        ]);

    }

    /**
     * Lists all HanziSplit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HanziSplitSearch();
        $currentPage = isset(Yii::$app->request->queryParams['page']) ? (int)Yii::$app->request->queryParams['page'] : 1;
        $authority = HanziTask::checkPagePermission(Yii::$app->user->id, $currentPage, HanziTask::TYPE_SPLIT);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        $dataProvider->pagination->pageSize = Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'authority' => $authority
        ]);
    }

    /**
     * 开始拆字工作
     * @return mixed
     */
    public function actionSplit()
    {
        $userId = Yii::$app->user->id;

        // 如果是回查阶段（seq=2），则检查角色权限
        $seq = (int)Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
        if ($seq == 2 && !User::isSecondSpliter($userId)) {
            throw new HttpException(401, '对不起，您不是回查员，无权进行回查。');
        }

        // 检查并设置当前任务包的session值
        $curSplitPackage = Yii::$app->session->get('curSplitPackage');
        if (!isset($curSplitPackage) || empty($curSplitPackage['id'])) {
            $curSplitPackage = WorkPackage::find()
                ->where(['userid' => $userId, 'type' => HanziTask::TYPE_SPLIT])
                ->andWhere('progress < volume')
                ->orderBy('created_at')
                ->one();
            Yii::$app->session->set('curSplitPackage', $curSplitPackage->attributes);
        }

        // 检查当前工作包是否完成
        $finishedCount = HanziUserTask::getFinishedWorkCountFrom($userId, HanziTask::TYPE_SPLIT, $curSplitPackage['created_at']);
        if ($finishedCount >= (int)$curSplitPackage['volume']) {
            // 当前工作包已完成，设置进度，跳转任务页面
            WorkPackage::updateProgress($curSplitPackage['id'], $finishedCount);
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_SPLIT, 'stage' => 1]);
            return;
        }

        // 检查当日工作是否已完成
        $finishedCountToday = (int)HanziUserTask::getFinishedWorkCountToday($userId, HanziTask::TYPE_SPLIT);
        if ($finishedCountToday >= (int)$curSplitPackage['daily_schedule']) {
            // 当日工作已完成，跳转打卡页面
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_SPLIT, 'stage' => 2]);
            return;
        }

        // 设置当前工作进度
        Yii::$app->session->set('curSplitProgress', "{$finishedCountToday}/{$curSplitPackage['daily_schedule']}");

        // 检查并设置当前工作页面的session值。
        $curPage = Yii::$app->session->get('curSplitPage');
        if (!isset($curPage) || empty($curPage['id'])) {
            // 寻找页面池中page值最小、状态为“初分配”“进行中”的页面，如果没有，则申请新页
            $curPage = HanziTask::getUnfinishedMinPage($userId, HanziTask::TYPE_SPLIT);
            if (empty($curPage)) {
                $curPage = HanziTask::getNewPage($userId, HanziTask::TYPE_SPLIT);
                if (empty($curPage)) {
                    throw new HttpException(401, '对不起，页面已经分配完毕，请您联系管理员。');
                }
            }
            Yii::$app->session->set('curSplitPage', $curPage->attributes);
        }

        // 寻找当前页中未完成的最小id
        $seq = $curPage['seq'];
        $curId = HanziSplit::getUnfinishedMinId($curPage['start_id'], $curPage['end_id'], $curPage['seq']);
        if ($seq == 1) {
            $this->redirect(['first', 'id' => $curId]);
        } elseif ($seq == 2) {
            $this->redirect(['second', 'id' => $curId]);
        } elseif ($seq == 3) {
            $this->redirect(['determine', 'id' => $curId]);
        } else {
            throw new HttpException(401, '对不起，您无权访问。');
        }
    }

    /**
     * Displays a single HanziSplit model.
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
     * Creates a new HanziSplit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HanziSplit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HanziSplit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $userId = Yii::$app->user->id;
        $seq = HanziTask::getSeq($userId, $id, HanziTask::TYPE_SPLIT);
        if ($seq == 1) {
            $this->redirect(['first', 'id' => $id]);
        } elseif ($seq == 2) {
            $this->redirect(['second', 'id' => $id]);
        } elseif ($seq == 3) {
            $this->redirect(['determine', 'id' => $id]);
        } else {
            throw new HttpException(401, '对不起，您无权访问。');
        }
    }

    /**
     * Updates an existing HanziSplit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    private function actionModify($id)
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
     * Finds the Hanzi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HanziSplit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HanziSplit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * @param string $id
     * @return mixed
     */
    public function actionFirst($id)
    {
        $seq = 1; // 初次拆分
        $userId = Yii::$app->user->id;

        if (!HanziTask::checkIdPermission($userId, $id, $seq, HanziTask::TYPE_SPLIT)) {
            throw new HttpException(401, '对不起，您无权访问。');
        }

        $next = Yii::$app->request->post('next');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 如果提交数据不为空，则添加一条完成任务，同时更新任务所在的页面状态
            if (!$model->isNew($seq)) {
                HanziUserTask::addItem($userId, $model->id, HanziTask::TYPE_SPLIT, HanziUserTask::SPLIT_WEIGHT, $seq);
            }
            // 检查当前页是否完成
            $curPage = Yii::$app->session->get('curSplitPage');
            if (isset($curPage) && $id >= (int)$curPage['end_id']) {
                HanziTask::updateStatus($curPage['id'], HanziTask::STATUS_COMPLETE);
                unset(Yii::$app->session['curSplitPage']);
            }
            return $next == 'true' ? $this->redirect(['split']) : $this->redirect(['view', 'id' => $model->id]);
        } else {
            // set default value
            if (!isset($model->hard10))
                $model->hard10 = 0;
            return $this->render('first', [
                'model' => $model,
                'seq' => $seq,
            ]);
        }
    }


    /**
     * @param string $id
     * @return mixed
     */
    public function actionSecond($id)
    {
        $seq = 2; // 二次拆分
        $userId = Yii::$app->user->id;

        // 检查页面权限
        if (!HanziTask::checkIdPermission($userId, $id, $seq, HanziTask::TYPE_SPLIT)) {
            throw new HttpException(401, '对不起，您无权访问。');
        }

        $next = Yii::$app->request->post('next');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 如果提交数据不为空，则添加一条完成任务，同时更新任务所在的页面状态
            if (!$model->isNew($seq)) {
                HanziUserTask::addItem($userId, $model->id, HanziTask::TYPE_SPLIT, HanziUserTask::SPLIT_WEIGHT, $seq);
            }
            // 检查当前页是否完成
            $curPage = Yii::$app->session->get('curSplitPage');
            if (isset($curPage) && $id >= (int)$curPage['end_id']) {
                HanziTask::updateStatus($curPage['id'], HanziTask::STATUS_COMPLETE);
                unset(Yii::$app->session['curSplitPage']);
            }
            return $next == 'true' ? $this->redirect(['split']) : $this->redirect(['view', 'id' => $model->id]);
        } else {
            // set default value
            if ($model->isNew($seq)) {
                $model->loadFromFirstSplit();
            }
            if (!isset($model->hard20))
                $model->hard20 = 0;
            return $this->render('second', [
                'model' => $model,
                'seq' => $seq,
            ]);
        }
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function actionDetermine($id)
    {
        $seq = 3; // 判取
        $userId = Yii::$app->user->id;

        if (!HanziTask::checkIdPermission($userId, $id, $seq, HanziTask::TYPE_SPLIT)) {
            throw new HttpException(401, '对不起，您无权访问。');
        }

        $next = Yii::$app->request->post('next');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (!$model->isNew($seq)) {
                HanziUserTask::addItem($userId, $model->id, HanziTask::TYPE_SPLIT, HanziUserTask::SPLIT_WEIGHT, $seq);
            }
            // 检查当前页是否完成
            $curPage = Yii::$app->session->get('curSplitPage');
            if (isset($curPage) && $id >= (int)$curPage['end_id']) {
                HanziTask::updateStatus($curPage['id'], HanziTask::STATUS_COMPLETE);
                unset(Yii::$app->session['curSplitPage']);
            }
            return $next == 'true' ? $this->redirect(['split']) : $this->redirect(['view', 'id' => $model->id]);
        } else {
            // set default value
            if (!isset($model->hard30))
                $model->hard30 = 0;
            return $this->render('determine', [
                'model' => $model,
                'seq' => $seq,
            ]);
        }
    }

}
