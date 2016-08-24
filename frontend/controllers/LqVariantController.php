<?php

namespace frontend\controllers;

use Yii;
use common\models\LqVariant;
use common\models\search\LqVariantSearch;
use common\models\HanziSet;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LqVariantController implements the CRUD actions for LqVariant model.
 */
class LqVariantController extends Controller
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
     * Import LqVariants from lq-variant-check.
     * @return mixed
     */
    public function actionImport()
    {
        $lastImportTime = Yii::$app->get('keyStorage')->get('frontend.last-lq-variant-import-time', null, false);
        $models = \common\models\LqVariantCheck::find()->where(['bconfirm' => 1])->andwhere(['>', 'updated_at', $lastImportTime])->orderBy('id')->all();

        $curImportTime = $lastImportTime;
        $sqls = [];
        foreach ($models as $model) {
            $source = HanziSet::SOURCE_OTHER;
            if (stripos($model->pic_name, "TW-") !== false) {
                $source = HanziSet::SOURCE_TAIWAN;
            } elseif (stripos($model->pic_name, "GL-") !== false) {
                $source = HanziSet::SOURCE_GAOLI;
            }
            $word = mb_strlen($model->variant_code2) == 1 ? $model->variant_code2 : null;
            $pic_name = mb_strlen($model->variant_code2) == 1 ? null : $model->variant_code2;
            $time = time();
            $sqls[] = "INSERT INTO lq_variant(source, word, pic_name, belong_standard_word_code, nor_var_type, created_at, updated_at) VALUES ($source, '$word', '$pic_name', '$model->belong_standard_word_code2', $model->nor_var_type2, $time, $time);";

            if ($curImportTime < $model->updated_at) {
                $curImportTime = $model->updated_at;
            }
        }

        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\import-lq-variant-'.date('Y-m-d-H-i-s', time()).'.txt', $contents);

        Yii::$app->get('keyStorage')->set('frontend.last-lq-variant-import-time', $curImportTime);
        echo 'success!';
        die;
    }

    /**
     * Lists all LqVariant models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LqVariantSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LqVariant model.
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
     * Creates a new LqVariant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LqVariant();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LqVariant model.
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
     * Deletes an existing LqVariant model.
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
     * Finds the LqVariant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LqVariant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LqVariant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
