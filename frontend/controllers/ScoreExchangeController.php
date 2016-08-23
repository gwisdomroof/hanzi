<?php

namespace frontend\controllers;

use common\models\ScoreExchange;
use common\models\search\ScoreExchangeSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * ScoreExchangeController implements the CRUD actions for ScoreExchange model.
 */
class ScoreExchangeController extends Controller
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
     * Lists all ScoreExchange models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new ScoreExchangeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all ScoreExchange models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ScoreExchangeSearch();
        $searchModel->userid = Yii::$app->user->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScoreExchange model.
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
     * Creates a new ScoreExchange model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionApply()
    {
        $model = new ScoreExchange();

        if ($model->load(Yii::$app->request->post())) {
            $model->score = ScoreExchange::scores()[$model->type];
            $totalScore = \common\models\HanziUserTask::getScore(Yii::$app->user->id);
            $changeScore = \common\models\ScoreExchange::getScore(Yii::$app->user->id);
            $leftScore = $totalScore - $changeScore;
            if ($leftScore < (int)$model->score) {
                throw new HttpException(400, '对不起，您的积分不足。');
            }
            $model->userid = Yii::$app->user->id;
            $model->status = ScoreExchange::STATUS_ASSIGNMENT;
            if ($model->save()) {
                return $this->redirect(['index', 'id' => $model->id]);
            } else {
                var_dump($model->getErrors());
                // throw new HttpException(404, '保存有误，请联系管理员。'); 
            }
        } else {
            return $this->render('apply', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScoreExchange model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        
        $params = Yii::$app->request->post();
        unset($params["ScoreExchange"]["user.username"]);
        if ($model->load($params) && $model->save()) {
            return $this->redirect(['admin', 'id' => $model->id]);
        } else {
            return $this->render('approve', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ScoreExchange model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!empty($model->status) && $model->status == ScoreExchange::STATUS_COMPLETE) {
            throw new HttpException(400, '已兑换申请不可删除。');
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ScoreExchange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ScoreExchange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScoreExchange::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
