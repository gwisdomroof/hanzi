<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziHyyt;
use common\models\HanziTask;
use common\models\HanziHyytSearch;
use common\models\HanziUserTask;
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


    /**
     * Lists all HanziHyyt models.
     * @return mixed
     */
    public function actionIndex($page=1, $seq=1)
    {
        $searchModel = new HanziHyytSearch();
        $param['HanziHyytSearch']['page'] = (int)$page;
        $models = $searchModel->search($param)->getModels();

        $userId = Yii::$app->user->id;
        $writeSeq = HanziTask::getSeqByPage($userId, $page, HanziTask::TYPE_INPUT); 
        $writeable = $writeSeq == $seq;
        $view  = $seq == 3 ? 'determine' : 'input';
       
        return $this->render($view, [
            'models' => $models,
            'curPage' => $page,
            'seq' => $seq,
            'writeable' => $writeable
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

        $userId = Yii::$app->user->id;
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $addScore = 0;
            if (!$model->isNew($seq)) {
                if (HanziUserTask::addItem($userId, $model->id, HanziTask::TYPE_INPUT, 1, $seq)) {
                    $addScore = 1;  #每增一项加一分
                }
            }
            return '{"status":"success", "id": ' . $id. ', "score": '. $addScore .'}';
        } else {
            return '{"status":"error", "id": ' . $id. '}';
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
