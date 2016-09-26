<?php

namespace frontend\controllers;

use Yii;
use common\models\LqVariant;
use common\models\LqVariantCheck;
use common\models\search\LqVariantCheckSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * LqVariantCheckController implements the CRUD actions for LqVariantCheck model.
 */
class LqVariantCheckController extends Controller
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
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionPages()
    {
        return $this->render('pages');
    }

    /**
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new LqVariantCheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LqVariantCheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionAdmin()
    {
        # 将post请求转为Get请求，以免get请求中的参数累加
        if (Yii::$app->request->post()) {
            $level = Yii::$app->request->post()['LqVariantCheckSearch']['level'];
            $bconfirm = Yii::$app->request->post()['LqVariantCheckSearch']['bconfirm'];
            $type = Yii::$app->request->post()['LqVariantCheckSearch']['nor_var_type'];
            $standard = Yii::$app->request->post()['LqVariantCheckSearch']['belong_standard_word_code'];
            $this->redirect(['admin', "level" => $level, "confirm" => $bconfirm, "standard" =>$standard, "type" => $type]);
        }
        $searchModel = new LqVariantCheckSearch();
        $searchModel->level = trim(Yii::$app->request->get('level'));
        $searchModel->bconfirm = trim(Yii::$app->request->get('confirm'));
        $searchModel->nor_var_type = trim(Yii::$app->request->get('type'));
        $searchModel->belong_standard_word_code = trim(Yii::$app->request->get('standard'));
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LqVariantCheck model.
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
     * Creates a new LqVariantCheck model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LqVariantCheck();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Updates an existing LqVariantCheck model.
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
     * @param string $id
     * @return mixed
     */
    public function actionModify($id)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            # 如果等级为ABCD，且confirm为是，则将这条数据插入龙泉异体字字典
            if ($model->level >= LqVariantCheck::LEVEL_FOUR && $model->bconfirm == 1) {
                LqVariant::addVariantFromCheck($model);
            }
            # 如果等级为一二三，且confirm为否，则将这条数据从龙泉异体字字典删除
            if ($model->level <= LqVariantCheck::LEVEL_THREE && $model->bconfirm == 0) {
                LqVariant::deleteVariantFromCheck($model);
            }
            return '{"status":"success", "id": ' . $id . '}';
        } else {
            return '{"status":"error", "id": ' . $id . '}';
        }
    }

    /**
     * Deletes an existing LqVariantCheck model.
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
     * Finds the LqVariantCheck model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LqVariantCheck the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LqVariantCheck::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
