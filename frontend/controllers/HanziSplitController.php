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
     * 将拆分结果导入hanzi_set表
     * @return mixed
     */
    public function actionExport()
    {
        $sqls = [];
        $models = HanziSplit::find()->orderBy('id')->all();
        foreach ($models as $model) {
            $splitIds = [];
            if (!empty($model->initial_split11) && trim($model->initial_split11) != $model->word)
                $splitIds[] = trim($model->initial_split11);
            if (!empty($model->initial_split12) && trim($model->initial_split12) != $model->word)
                $splitIds[] = trim($model->initial_split12);
            if (!empty($model->deform_split10) && trim($model->deform_split10) != $model->word)
                $splitIds[] = trim($model->deform_split10);
            if (!empty($model->similar_stock10) && trim($model->similar_stock10) != $model->word)
                $splitIds[] = trim($model->similar_stock10);
            if (!empty($model->initial_split21) && trim($model->initial_split21) != $model->word)
                $splitIds[] = trim($model->initial_split21);
            if (!empty($model->initial_split22) && trim($model->initial_split22) != $model->word)
                $splitIds[] = trim($model->initial_split22);
            if (!empty($model->deform_split20) && trim($model->deform_split20) != $model->word)
                $splitIds[] = trim($model->deform_split20);
            if (!empty($model->similar_stock20) && trim($model->similar_stock20) != $model->word)
                $splitIds[] = trim($model->similar_stock20);

            $splitIds = array_unique($splitIds);
            $minSplit = implode('；', $splitIds);
            if (empty($minSplit))
                $sqls[] = "$model->id\t''";
            else
                $sqls[] = "$model->id\t'{$minSplit}'";

        }
        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\hanzi-split-unicode.txt', $contents);

        echo "success!";
        die;

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
     * 开始高丽藏拆字工作
     * @return mixed
     */
    public function actionGaoliSplit()
    {
        $userId = Yii::$app->user->id;
        $seq = (int)Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
        // 如果是查漏阶段（seq=3），则直接从全局分配漏查的id
        if ($seq == 3) {
            $leakHanzi = HanziSplit::getLeakHanzi();
            if (!$leakHanzi) {
                throw new HttpException(401, '随喜！查漏工作已完成。');
            }
            $_seq = (int)$leakHanzi['seq'];
            $_id = (int)$leakHanzi['id'];
            if ($_seq == 1) {
                return $this->redirect(['first', 'id' => $_id]);
            } elseif ($_seq == 2) {
                return $this->redirect(['second', 'id' => $_id]);
            }
        }


        // 如果是回查阶段（seq=2），则检查角色权限
        if ($seq == 2 && !User::isSecondSpliter($userId)) {
            throw new HttpException(401, '对不起，您不是回查员，无权进行回查。');
        }

        // 检查并设置当前任务包的session值
        $curSplitPackage = Yii::$app->session->get('curSplitPackage');
        if (!isset($curSplitPackage) || empty($curSplitPackage['id'])) {
            $curSplitPackage = WorkPackage::find()
                ->where(['userid' => $userId, 'type' => HanziTask::TYPE_GAOLI_SPLIT])
                ->andWhere('progress < volume')
                ->orderBy('created_at')
                ->one();
            Yii::$app->session->set('curSplitPackage', $curSplitPackage->attributes);
        }

        // 检查当前工作包是否完成
        $finishedCount = HanziUserTask::getFinishedWorkCountFrom($userId, HanziTask::TYPE_GAOLI_SPLIT, $curSplitPackage['created_at']);
        if ($finishedCount >= (int)$curSplitPackage['volume']) {
            // 当前工作包已完成，设置进度，跳转任务页面
            WorkPackage::updateProgress($curSplitPackage['id'], $finishedCount);
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_GAOLI_SPLIT, 'stage' => 1]);
            return;
        }

        // 检查当日工作是否已完成
        $finishedCountToday = (int)HanziUserTask::getFinishedWorkCountToday($userId, HanziTask::TYPE_GAOLI_SPLIT);
        if ($finishedCountToday >= (int)$curSplitPackage['daily_schedule']) {
            // 当日工作已完成，跳转打卡页面
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_GAOLI_SPLIT, 'stage' => 2]);
            return;
        }

        // 设置当前工作进度
        Yii::$app->session->set('curSplitProgress', "{$finishedCountToday}/{$curSplitPackage['daily_schedule']}");

        // 检查并设置当前工作页面的session值。
        $curPage = Yii::$app->session->get('curSplitPage');
        if (!isset($curPage) || empty($curPage['id'])) {
            // 寻找页面池中page值最小、状态为“初分配”“进行中”的页面，如果没有，则申请新页
            $curPage = HanziTask::getUnfinishedMinPage($userId, HanziTask::TYPE_GAOLI_SPLIT);

            if (empty($curPage)) {
                $curPage = HanziTask::getNewPage($userId, HanziTask::TYPE_GAOLI_SPLIT);
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
     * 开始拆字工作
     * @return mixed
     */
    public function actionSplit()
    {
        $userId = Yii::$app->user->id;

        $seq = (int)Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
        // 如果是查漏阶段（seq=3），则直接从全局分配漏查的id
        if ($seq == 3) {
            $leakHanzi = HanziSplit::getLeakHanzi();
            if (!$leakHanzi) {
                throw new HttpException(401, '随喜！查漏工作已完成。');
            }
            $_seq = (int)$leakHanzi['seq'];
            $_id = (int)$leakHanzi['id'];
            if ($_seq == 1) {
                return $this->redirect(['first', 'id' => $_id]);
            } elseif ($_seq == 2) {
                return $this->redirect(['second', 'id' => $_id]);
            }
        }

        // 如果是回查阶段（seq=2），则检查角色权限
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

        $taskType = HanziSplit::getSplitTaskType($id);
        if (!HanziTask::checkIdPermission($userId, $id, $seq, $taskType)) {
            throw new HttpException(401, '对不起，您无权访问。');
        }

        $next = Yii::$app->request->post('next');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 如果提交数据不为空，则添加一条完成任务，同时更新任务所在的页面状态
            if (!$model->isNew($seq)) {
                HanziUserTask::addItem($userId, $model->id, $taskType, HanziUserTask::SPLIT_WEIGHT, $seq);
            }
            // 检查当前页是否完成
            $curPage = Yii::$app->session->get('curSplitPage');
            if (isset($curPage) && $id >= (int)$curPage['end_id']) {
                HanziTask::updateStatus($curPage['id'], HanziTask::STATUS_COMPLETE);
                unset(Yii::$app->session['curSplitPage']);
            }
            $redirect = $taskType == HanziTask::TYPE_SPLIT ? 'split' : 'gaoli-split';
            return $next == 'true' ? $this->redirect([$redirect]) : $this->redirect(['view', 'id' => $model->id]);
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

        $taskType = HanziSplit::getSplitTaskType($id);
        // 检查页面权限
        if (!HanziTask::checkIdPermission($userId, $id, $seq, $taskType)) {
            throw new HttpException(401, '对不起，您无权访问。');
        }

        $next = Yii::$app->request->post('next');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // 如果提交数据不为空，则添加一条完成任务，同时更新任务所在的页面状态
            if (!$model->isNew($seq)) {
                HanziUserTask::addItem($userId, $model->id, $taskType, HanziUserTask::SPLIT_WEIGHT, $seq);
            }
            // 检查当前页是否完成
            $curPage = Yii::$app->session->get('curSplitPage');
            if (isset($curPage) && $id >= (int)$curPage['end_id']) {
                HanziTask::updateStatus($curPage['id'], HanziTask::STATUS_COMPLETE);
                unset(Yii::$app->session['curSplitPage']);
            }
            $redirect = $taskType == HanziTask::TYPE_SPLIT ? 'split' : 'gaoli-split';
            return $next == 'true' ? $this->redirect($redirect) : $this->redirect(['view', 'id' => $model->id]);
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

        $taskType = HanziSplit::getSplitTaskType($id);
        if (!HanziTask::checkIdPermission($userId, $id, $seq, $taskType)) {
            throw new HttpException(401, '对不起，您无权访问。');
        }

        $next = Yii::$app->request->post('next');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (!$model->isNew($seq)) {
                HanziUserTask::addItem($userId, $model->id, $taskType, HanziUserTask::SPLIT_WEIGHT, $seq);
            }
            // 检查当前页是否完成
            $curPage = Yii::$app->session->get('curSplitPage');
            if (isset($curPage) && $id >= (int)$curPage['end_id']) {
                HanziTask::updateStatus($curPage['id'], HanziTask::STATUS_COMPLETE);
                unset(Yii::$app->session['curSplitPage']);
            }
            $redirect = $taskType == HanziTask::TYPE_SPLIT ? 'split' : 'gaoli-split';
            return $next == 'true' ? $this->redirect($redirect) : $this->redirect(['view', 'id' => $model->id]);
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
