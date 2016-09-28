<?php

namespace frontend\controllers;

use Yii;
use common\models\CommonPage;
use common\models\search\CommonPageSearch;
use common\models\HanziTask;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommonPageController implements the CRUD actions for CommonPage model.
 */
class CommonPageController extends Controller
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
     * Lists all CommonPage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommonPageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CommonPage model.
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
     * Creates a new CommonPage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CommonPage();

        if ($model->load(Yii::$app->request->post())) {
            $page = $model->page;
            if (is_numeric($page) && $model->save()) {
                return $this->redirect(['index']);
            } elseif (preg_match('/(\d+)-(\d+)/', $page, $matches)) {
                for ($i = (int)$matches[1]; $i <= (int)$matches[2]; $i++) {
                    $innerModel = new CommonPage();
                    $innerModel->task_type = $model->task_type;
                    $innerModel->page = $i;
                    if ($innerModel->task_type == HanziTask::TYPE_SPLIT)
                        $innerModel->seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
                    elseif ($innerModel->task_type == HanziTask::TYPE_INPUT)
                        $innerModel->seq = Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false);
                    if ($innerModel->validate())
                        $innerModel->save();
                }
                return $this->redirect(['index']);
            } elseif (preg_match("/(\d+,)+\d+/", $page, $matches)) {
                $pages = explode(",", $page);
                foreach ($pages as $i) {
                    $innerModel = new CommonPage();
                    $innerModel->task_type = $model->task_type;
                    $innerModel->page = $i;
                    if ($innerModel->task_type == HanziTask::TYPE_SPLIT)
                        $innerModel->seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
                    elseif ($innerModel->task_type == HanziTask::TYPE_INPUT)
                        $innerModel->seq = Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false);
                    if ($innerModel->validate())
                        $innerModel->save();
                }
                return $this->redirect(['index']);
            } else {
                $model->addError('page', '页面必须为整数或整数的范围。');
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CommonPage model.
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
     * Deletes an existing CommonPage model.
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
     * Finds the CommonPage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CommonPage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommonPage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
