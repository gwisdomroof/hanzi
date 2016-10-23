<?php

namespace frontend\controllers;

use common\models\GlVariant;
use common\models\GltwDedup;
use common\models\search\GltwDedupSearch;
use common\models\HanziSet;
use common\models\User;
use common\models\HanziTask;
use common\models\HanziUserTask;
use common\models\WorkPackage;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * GltwDedupController implements the CRUD actions for GltwDedup model.
 * 高丽藏异体字去重工作，分两步：
 * 一、正字去重。
 * 用一张表{hanzi_gltw_dedup}来记录正字的去重结果，去重结果记录到{gl_variant}表的duplicate_id中，然后可以作为异体字去重的工作表。
 * 二、异体字去重
 * 从{hanzi_gltw_dedup}中读取需要去重的正字进行去重，去重结果记录到{gl_variant}表的duplicate_id中。
 */
class GltwDedupController extends Controller
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
     * Save the duplicate data.
     * @return mixed
     */
    public function actionSave()
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        if (!isset(Yii::$app->request->post()['glCode'])) {
            return '{"status":"error", "msg": "高丽异体字编码不能为空."}';
        }

        $glCode = trim(Yii::$app->request->post()['glCode']);
        $glVariant = GlVariant::find()->where(['source' => HanziSet::SOURCE_GAOLI])
            ->andWhere(['or', ['word' => $glCode], ['pic_name' => $glCode]])
            ->one();
        if (empty($glVariant)) {
            return '{"status":"error", "msg": "未找到编码对应的高丽藏异体字."}';
        }

        $twCode = trim(Yii::$app->request->post()['twCode']);
        $twVariant = HanziSet::find()->where(['source' => HanziSet::SOURCE_TAIWAN])
            ->andWhere(['or', ['word' => $twCode], ['pic_name' => $twCode]])
            ->one();
        if (empty($twVariant)) {
            return '{"status":"error", "msg": "未找到编码对应的台湾异体字."}';
        }

        // 不同阶段，存储在不同的字段
        $seq = Yii::$app->request->post()['seq'];
        $duplicate_id = "duplicate_id{$seq}";
        $glVariant->$duplicate_id = $twCode;
        if ($glVariant->save()) {
            $addScore = 0;
            $seq = Yii::$app->get('keyStorage')->get('frontend.current-dedup-stage', null, false);
            if (HanziUserTask::addItem(Yii::$app->user->id, $glVariant->id, HanziTask::TYPE_DEDUP, HanziUserTask::DEDUP_WEIGHT, $seq)) {
                $addScore = HanziUserTask::DEDUP_WEIGHT;  # 每增一项加一分
            }
            return '{"status":"success", "id": ' . $glVariant->id . ', "score": ' . $addScore . '}';
        }

        return '{"status":"error", "msg": "保存错误，请联系管理员."}';
    }


    /**
     * 初始化工作
     * 和拆字及异体字录入不同，去重时，以找到的重复字来积分，以所做的页面数来计算工作量。
     * @return bool
     */
    private function dedupInitial()
    {
        $userId = Yii::$app->user->id;
        // 检查并设置当前任务包的session值
        $curDedupPackage = Yii::$app->session->get('curDedupPackage');
        if (!isset($curDedupPackage) || empty($curDedupPackage['id'])) {
            $curDedupPackage = WorkPackage::find()
                ->where(['userid' => $userId, 'type' => HanziTask::TYPE_DEDUP])
                ->andWhere('progress < volume')
                ->orderBy('created_at')
                ->one();
            if (!empty($curDedupPackage))
                Yii::$app->session->set('curDedupPackage', $curDedupPackage->attributes);
        }

        // 检查当前工作包是否完成
        $finishedCount = (int)HanziTask::getFinishedWorkCountFrom($userId, HanziTask::TYPE_DEDUP, $curDedupPackage['created_at']);
        if ($finishedCount >= (int)$curDedupPackage['volume']) {
            // 当前工作包已完成，设置进度
            WorkPackage::updateProgress($curDedupPackage['id'], $finishedCount);
            // 跳转任务页面
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_DEDUP, 'stage' => 1]);
            return false;
        }

        // 检查当日工作是否已完成
        $finishedCountToday = (int)HanziTask::getFinishedWorkCountToday($userId, HanziTask::TYPE_DEDUP);
        // 设置当前工作进度
        Yii::$app->session->set('curDedupProgress', "{$finishedCountToday}/{$curDedupPackage['daily_schedule']}");
        if ($finishedCountToday >= (int)$curDedupPackage['daily_schedule']) {
            // 当日工作已完成，跳转打卡页面
            $this->redirect(['work-package/info', 'type' => HanziTask::TYPE_DEDUP, 'stage' => 2]);
            return false;
        }

        // 检查并设置当前工作页面的session值。
        $curPage = Yii::$app->session->get('curDedupPage');
        if (!isset($curPage) || empty($curPage['id'])) {
            // 寻找页面池中page值最小、状态为“初分配”“进行中”的页面，如果没有，则申请新页
            $curPage = HanziTask::getUnfinishedMinPage($userId, HanziTask::TYPE_DEDUP);
//            var_dump($curPage);
//            die;
            if (empty($curPage)) {
                $curPage = HanziTask::getNewPage($userId, HanziTask::TYPE_DEDUP);
            }
            Yii::$app->session->set('curDedupPage', $curPage->attributes);
        }
        return true;
    }

    /**
     * 决策下一个去重的id号。
     * 1、保存当前去重字的状态；
     * 2、寻找当前阶段的最小ID；
     * 3、设置该id状态为被占用；
     * @return mixed
     */
    public function actionNext()
    {
        // 处理当前任务
        if (!empty(Yii::$app->request->get('id'))) {
            // 保存当前去重字的状态，取消占用
            $id = (int)Yii::$app->request->get('id');
            $model = GltwDedup::findOne($id);
            $model->status = (int)$model->status + 1;
            $model->is_occupied = 0;
            if (!$model->save()) {
                var_dump($model->getErrors());
                die;
            }
            // 设置当前任务的状态
            if (isset(Yii::$app->session['curDedupPage'])) {
                $id = (int)Yii::$app->session['curDedupPage']['id'];
                $model = HanziTask::findOne($id);
                $model->status = HanziTask::STATUS_COMPLETE;
                $model->save();
            }
        }

        // 销毁当前任务
        unset(Yii::$app->session['curDedupPage']);

        // 如果是回查阶段（seq=2），则检查角色权限
        $seq = (int)Yii::$app->get('keyStorage')->get('frontend.current-dedup-stage', null, false);
        if ($seq == 2 && !User::isSecondDeduper(Yii::$app->user->id)) {
            throw new HttpException(401, '对不起，您不是去重回查员，无权进行回查。');
        }

        // 执行初始化检查，转下一个字
        if ($this->dedupInitial()) {
            // 跳转工作页面
            $curPage = Yii::$app->session->get('curDedupPage');
            // hanzi_gltw_dedup的id对应于
            $this->redirect(['dedup', 'id' => $curPage['page']]);
        };

    }

    /**
     * Lists all GltwDedup models.
     * @return mixed
     */
    public function actionDedup($id, $seq = 1)
    {
        $model = GltwDedup::findOne($id);

        if ($model->status === GltwDedup::STATUS_NONEED) {
            throw new HttpException(401, '不需要去重。');
        }

        // 台湾异体字
        $twNormals = empty($model->unicode) ? $model->gaoli : $model->unicode;
        $twModel = HanziSet::find()->where(['like', 'word', $twNormals])
            ->andWhere(['source' => HanziSet::SOURCE_TAIWAN])
            ->one();
        if (!empty($twModel) && $twModel->nor_var_type != HanziSet::TYPE_NORMAL_PURE)
            $twNormals = $twNormals . str_replace(';', '', $twModel->belong_standard_word_code);

        $twVariants = HanziSet::find()->where(['~', 'belong_standard_word_code', "[{$twNormals}]"])
            ->andWhere(['source' => HanziSet::SOURCE_TAIWAN])
            ->orderBy('id')
            ->all();

        // 处理结果
        $twData = [];
        $twNormalArr = preg_split('//u', $twNormals, -1, PREG_SPLIT_NO_EMPTY);
        $twNormalArr = array_unique($twNormalArr);
        foreach ($twVariants as $variant) {
            foreach ($twNormalArr as $normal) {
                if (strpos($variant->belong_standard_word_code, $normal) !== false) {
                    $twData[$normal][] = $variant;
                    continue;
                }
            }
        }

        // 高丽异体字
        $glNormals = $model->gaoli;
        $glVariants = GlVariant::find()->where(['~', 'belong_standard_word_code', "[{$glNormals}]"])
            ->orderBy('id')
            ->all();
        $glData[$glNormals] = $glVariants;
        // 如果是相似字形，则把高丽正字也加入去重队列
        if ($model->relation = GltwDedup::RELATION_SIMILAR) {
            $glData[$glNormals][] = GlVariant::find()->where(['word' => $glNormals])
                ->andWhere(['source' => HanziSet::SOURCE_GAOLI])
                ->one();
        }

        // 权限检查
        $permission = HanziTask::checkPagePermission(Yii::$app->user->id, $id, $seq, HanziTask::TYPE_DEDUP);
        if (!$permission)
            $permission = 0;
        return $this->render('dedup', [
            'model' => $model,
            'twData' => $twData,
            'glData' => $glData,
            'permission' => $permission,
            'seq' => $seq
        ]);
    }


    /**
     * Lists all GltwDedup models.
     * @return mixed
     */
    private function actionCheck()
    {
        $sqls = [];
        $models = GltwDedup::find()->where(['!=', 'relation', GltwDedup::RELATION_EMPTY])
            ->all();
        foreach ($models as $model) {
            // 台湾异体字
            $twNormals = empty($model->unicode) ? $model->gaoli : $model->unicode;
            $twModel = HanziSet::find()->where(['like', 'word', $twNormals])
                ->andWhere(['source' => HanziSet::SOURCE_TAIWAN])
                ->one();
            if (!empty($twModel) && $twModel->nor_var_type != HanziSet::TYPE_NORMAL_PURE)
                $twNormals = $twNormals . str_replace(';', '', $twModel->belong_standard_word_code);

            $twVariantsExist = HanziSet::find()->where(['~', 'belong_standard_word_code', "[{$twNormals}]"])
                ->andWhere(['source' => HanziSet::SOURCE_TAIWAN])
                ->exists();

            if ($twVariantsExist) {
                $model->status = GltwDedup::STATUS_INITIAL;
            } else {
                $model->status = GltwDedup::STATUS_NONEED;
            }

            $sqls[] = "update hanzi_gltw_dedup set status = {$model->status} where id = {$model->id};";
        }

        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\gltw-dedup-noneed.txt', $contents);

        echo "success!";
        die;
    }


    /**
     * Lists all GltwDedup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GltwDedupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // 是否有审查的权限
        $authority = true;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'authority' => $authority
        ]);
    }

    /**
     * Displays a single GltwDedup model.
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
     * Creates a new GltwDedup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GltwDedup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GltwDedup model.
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
     * Deletes an existing GltwDedup model.
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
     * Finds the GltwDedup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GltwDedup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GltwDedup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
