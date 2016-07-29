<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziSet;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * HanziController implements the CRUD actions for HanziSet model.
 */
class DeDupController extends Controller
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
     * 设置高丽藏页码
     * @return mixed
     */
    private function actionSetpage()
    {
        
        $curPage = 1;
        $curCount = 0;
        $pageCount = 50;
        $models = \common\models\HanziGaoliDedup::find()->orderBy('id')->all();

        $sqls = [];
        $index = 1;
        foreach ($models as $model) {
            $curCount += (int)$model->zmcnt;
            if ($curCount <= $pageCount) {
                $model->page = $curPage;
                $sqls[] = "update hanzi_gaoli_dedup set page=$curPage where id = $model->id;";
            } else {
                $curPage++;
                $sqls[] = "update hanzi_gaoli_dedup set page=$curPage where id = $model->id;";
                $curCount = 0;
            }
            // if ($index++ > 100)
            //     break;
        }
        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\set-gl-page.txt', $contents);

        echo "success!";
        die;

    }

    /**
     * 高丽藏内部，按照郑码去重
     * 需要去重的郑码为后台参数“frontend.gl-dedup”存储的内容
     * @return mixed
     */
    public function actionGaoli($page)
    {
        $page = (int)trim($page);
        $glDedup = \common\models\HanziGaoliDedup::find()->orderBy('id')->where(['page' => $page])->asArray()->all();
        $models = HanziSet::find()->orderBy('zhengma')->where(['source'=>HanziSet::SOURCE_GAOLI, 'zhengma'=>$glDedup])->all();

        return $this->render('glDedup', [
            'models' => $models,
            'curPage' => $page
        ]);

    }

    /**
     * 高丽藏内部去重
     * @return mixed
     */
    public function actionGlSave($id)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $id = (int)trim($id);
        $model = HanziSet::findOne($id);
        if ($model == null) {
            // throw new NotFoundHttpException('The requested page does not exist.');
            return '{"status":"error", "msg": "data not found."}';
        }

        if (isset(Yii::$app->request->post()['HanziSet']['duplicate_id'])) {
            $model->duplicate_id = Yii::$app->request->post()['HanziSet']['duplicate_id'];
            $dupModel = HanziSet::find()->where(['pic_name'=>$model->duplicate_id, 'source'=>HanziSet::SOURCE_GAOLI])->one();
            $dupModel->duplicate_id = empty($dupModel->duplicate_id) ? $model->pic_name : $dupModel->duplicate_id . ';' . $model->pic_name;
            $dupArr = explode(';', $dupModel->duplicate_id);
            $dupModel->duplicate_id = implode(';', array_unique($dupArr));
            if ($dupModel !== null && $model->save() && $dupModel->save())
                return '{"status":"success", "dupId": "' .  $dupModel->id. '", "dupValue": "' . $dupModel->duplicate_id . '"}';
        } 
        
        return '{"status":"error"}';
    }

}