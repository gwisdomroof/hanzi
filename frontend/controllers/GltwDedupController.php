<?php

namespace frontend\controllers;

use Yii;
use common\models\GltwDedup;
use common\models\search\GltwDedupSearch;
use common\models\HanziSet;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * GltwDedupController implements the CRUD actions for GltwDedup model.
 * 高丽藏异体字去重工作，分两步：
 * 一、正字去重。
 * 用一张表{hanzi_gltw_dedup}来记录正字的去重结果，去重结果记录到{hanzi_set}表的duplicate_id中，然后可以作为异体字去重的工作表。
 * 二、异体字去重
 * 从{hanzi_gltw_dedup}中读取需要去重的正字进行去重，去重结果记录到{hanzi_set}表的duplicate_id中。
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
        $glVariant = HanziSet::find()->where(['source' => HanziSet::SOURCE_GAOLI])
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

        $glVariant->duplicate_id = $twCode;
        if ($glVariant->save()) {
            return '{"status":"success", "msg": "保存成功."}';
        }

        return '{"status":"error", "msg": "保存错误，请联系管理员."}';
    }


    /**
     * Lists all GltwDedup models.
     * @return mixed
     */
    public function actionDedup($id)
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
        $glVariants = HanziSet::find()->where(['~', 'belong_standard_word_code', "[{$glNormals}]"])
            ->andWhere(['source' => HanziSet::SOURCE_GAOLI])
            ->andWhere(['!=', 'nor_var_type', HanziSet::TYPE_NORMAL_PURE])
            ->orderBy('id')
            ->all();
        $glData[$glNormals] = $glVariants;
        // 如果是相似字形，则把高丽正字也加入去重队列
        if ($model->relation = GltwDedup::RELATION_SIMILAR) {
            $glData[$glNormals][] = HanziSet::find()->where(['word' => $glNormals])
                ->andWhere(['source' => HanziSet::SOURCE_GAOLI])
                ->one();
        }

        return $this->render('dedup', [
            'model' => $model,
            'twData' => $twData,
            'glData' => $glData,
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

            if($twVariantsExist) {
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
     * Lists all GltwDedup models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new GltwDedupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);

        // 是否有审查的权限
        $authority = true;

        return $this->render('admin', [
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
