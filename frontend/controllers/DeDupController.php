<?php

namespace frontend\controllers;

use common\models\GlVariant;
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
     * 去重后，需要设置duplicate两个字段
     * duplicate为1，表示这条记录属于从数据。
     * 同时修改这一组异体字的nor_var_type和belong_standard_word_code。
     * @return mixed
     */
    public function actionSetDup()
    {
        $models = HanziSet::find()->orderBy(['zhengma' => SORT_ASC, 'id' => SORT_ASC])->where(['source' => HanziSet::SOURCE_GAOLI])->andWhere(['!=', 'duplicate_id', ''])->all();

        $handledIds = [];
        foreach ($models as $model) {
            if (in_array($model->id, $handledIds)) {
                continue;
            }

            $mainModel = $model;
            $subModels = [];
            $standardWords = [];
            $subIds = [];
            if (strpos($model->duplicate_id, ';') == false) {
                $glDedup = HanziSet::find()->where(['pic_name' => $model->duplicate_id, 'source' => HanziSet::SOURCE_GAOLI])->one();
                if (!empty($glDedup->duplicate_id) && strpos($glDedup->duplicate_id, ';') !== false) {
                    $mainModel = $glDedup;
                    $picNames = explode(';', $glDedup->duplicate_id);
                    $subModels = HanziSet::find()->where(['pic_name' => $picNames, 'source' => HanziSet::SOURCE_GAOLI])->all();
                } else {
                    $subModels[] = $glDedup;
                }
            } else {
                $picNames = explode(';', $model->duplicate_id);
                $subModels = HanziSet::find()->where(['pic_name' => $picNames, 'source' => HanziSet::SOURCE_GAOLI])->all();
            }

            $handledIds[] = $mainModel->id;
            $standardWords[] = $mainModel->belong_standard_word_code;
            foreach ($subModels as $subModel) {
                $standardWords[] = $subModel->belong_standard_word_code;
                $handledIds[] = $subModel->id;
                $subIds[] = $subModel->id;
            }

            # 主model
            $mainModel->nor_var_type = HanziSet::TYPE_VARIANT_WIDE;
            $mainModel->belong_standard_word_code = implode(';', $standardWords);
            if (!$mainModel->save()) {
                var_dump($mainModel->getErrors());
            }

            # 从model
            Yii::$app->db->createCommand()->update('hanzi_set', ['duplicate' => 1, 'nor_var_type' => HanziSet::TYPE_VARIANT_WIDE, 'belong_standard_word_code' => implode(';', $standardWords)], ['id' => $subIds])->execute();
        }

        echo 'finished!';
        die;
    }

    /**
     * 提取高丽藏内部去重的重复编码
     * @return mixed
     */
    public function actionExtract()
    {
        $models = HanziSet::find()->where("source = 4 and  duplicate_id != ''")->asArray()->all();
        $redundants = [];
        $redundantids = [];
        foreach ($models as $model) {
            $inArray = false;
            $dupIds = explode(";", $model['duplicate_id']);
            $dupIds[] = $model['pic_name'];
            foreach ($dupIds as $dupId) {
                if (in_array($dupId, $redundants))
                    $inArray = true;
            }
            if (!$inArray) {
                $redundants[] = $model['pic_name'];
                $redundantids[] = $model['id'];
            }
        }

        foreach ($redundantids as $redundant) {
            echo $redundant . '<br/>';
        }
        die;

    }

    /**
     * 高丽藏内部，按照郑码去重
     * 需要去重的郑码为后台参数“frontend.gl-dedup”存储的内容
     * @return mixed
     */
    public function actionGaoli($page = 1)
    {
        $page = (int)trim($page);
        $glDedup = \common\models\HanziGaoliDedup::find()->orderBy('id')->where(['page' => $page])->asArray()->all();
        $models = HanziSet::find()->orderBy(['zhengma' => SORT_ASC, 'id' => SORT_ASC])->where(['source' => HanziSet::SOURCE_GAOLI, 'zhengma' => $glDedup])->all();

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
            $score = Yii::$app->request->post()['HanziSet']['duplicate_id'];
            if (!empty($score)) {
                $model->duplicate_id = Yii::$app->request->post()['HanziSet']['duplicate_id'];
                $dupModel = HanziSet::find()->where(['pic_name' => $model->duplicate_id, 'source' => HanziSet::SOURCE_GAOLI])->one();
                $dupModel->duplicate_id = empty($dupModel->duplicate_id) ? $model->pic_name : $dupModel->duplicate_id . ';' . $model->pic_name;
                $dupArr = explode(';', $dupModel->duplicate_id);
                $dupModel->duplicate_id = implode(';', array_unique($dupArr));
                if ($dupModel !== null && $model->save() && $dupModel->save())
                    return '{"status":"success", "dupId": "' . $dupModel->id . '", "dupValue": "' . $dupModel->duplicate_id . '"}';
            } else {
                $dupModel = HanziSet::find()->where(['pic_name' => $model->duplicate_id, 'source' => HanziSet::SOURCE_GAOLI])->one();
                $dupModel->duplicate_id = preg_replace('/' . $model->pic_name . ';?/', '', $dupModel->duplicate_id);
                $model->duplicate_id = '';
                if ($dupModel !== null && $model->save() && $dupModel->save())
                    return '{"status":"success", "dupId": "' . $dupModel->id . '", "dupValue": "' . $dupModel->duplicate_id . '"}';
            }
        }

        return '{"status":"error"}';
    }

}