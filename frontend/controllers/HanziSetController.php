<?php

namespace frontend\controllers;

use common\models\HanziSet;
use common\models\search\HanziSetSearch;
use common\models\LqVariant;
use common\models\search\LqVariantSetSearch;
use common\models\HanziSplit;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HanziController implements the CRUD actions for HanziSet model.
 */
class HanziSetController extends Controller
{

    private $hanziIds = [];

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
     * 部件笔画检字法
     * @return mixed
     */
    public function actionSearch()
    {
        # 将post请求转为Get请求，以免get请求中的参数累加
        if (Yii::$app->request->post()) {
            $this->redirect(['search', 'param' => Yii::$app->request->post()['HanziSetSearch']['param']]);
        }

        $data = [];
        $pagination = new \yii\data\Pagination(['totalCount' => 0]);
        $message = null;
        $hanziSearch = new HanziSetSearch();
        if (isset(Yii::$app->request->get()['param'])) {
            $hanziSearch->param = trim(Yii::$app->request->get()['param']);
        }

        $mode = 'wsearch';
        if (!empty($hanziSearch->param) && $hanziSearch->validate()) {
            if (preg_match("/^!/", $hanziSearch->param)) {    # 反查汉字拆分序列
                $data = $hanziSearch->rsearch();
                $mode = 'rsearch';
            } else {    # 部件笔画检字
                $query = $hanziSearch->wsearch();
                $count = $query->count();
                $message = $count == 0 ? "查询结果为空。" : "共检索到" . $count . "条数据。";
                $pagination = new \yii\data\Pagination(['totalCount' => $count, 'pageSize' => 100]);
                $data = $query->orderBy('id')->offset($pagination->offset)->limit(100)->all();
            }
        }

        return $this->render('search', [
            'hanziSearch' => $hanziSearch,
            'mode' => $mode,
            'data' => $data,
            'pagination' => $pagination,
            'message' => $message,
        ]);
    }

    /**
     * 检查高丽台湾拆字结果
     * @param $str
     */
    public function actionCheck()
    {
        $dups = require_once('_todo.php');

        $existed = [];
        $notExisted = [];
        $notFound = [];
        foreach ($dups as $key => $value) {
            $valueArr = explode(';', $value);
            $picName = "'$key',";
            foreach ($valueArr as $item) {
                $picName .= "'$item',";
            }
            $picName = trim($picName, ',');
            $models = HanziSet::find()->where("pic_name in ({$picName}) ")->all();
            $normals = [];
            foreach ($models as $model) {
                $normals[] = $model->belong_standard_word_code;
            }
            $normals = array_unique($normals);
            $normals = implode(',', $normals);
            if (strpos($normals, ',') !== false) {
                $existed[] = "{$key}\t{$value}\t{$normals}";
            } else {
                $notExisted[] = "{$key}\t{$value}\t{$normals}";
            }
//            if (!empty($model)) {
//                if (!empty($model->pic_name)) {
//                    $existed[] = "{$key}\t{$value}\t{$model->pic_name}";
//                } elseif (!empty($model->pic_name)) {
//                    $notExisted[] = "{$key}\t{$value}\t{$model->word}";
//                }
//            } else {
//                $notFound[] = "\t{$key}\t{$value}\tnot found";
//            }

        }

        echo "=====not existed =====<br/>";
        foreach ($notExisted as $i) {
            echo "{$i}<br/>";
        }

        echo "=====existed=====<br/>";
        foreach ($existed as $i) {
            echo "{$i}<br/>";
        }

        echo "=====not found=====<br/>";
        foreach ($notFound as $i) {
            echo "{$i}<br/>";
        }

        die;
    }


    /**
     * 生成部件序列
     * @return mixed
     */
    public function actionGenerateSerial()
    {
        mb_internal_encoding("UTF-8");
        mb_regex_encoding("UTF-8");
        $this->hanziIds = require_once('_unicodeMinSplitIds.php');

        $models = HanziSet::find()
            ->where("(min_split is not null or deform_split is not null)")
            ->andWhere("id > 242051")
//            ->andWhere("id = 242052")
//            ->limit(2)
            ->orderBy('id')
            ->all();

        $index = 1;
        $output = [];
        foreach ($models as $model) {
            $minIds = $model->min_split . $model->deform_split;
            if(!empty($model->min_split) && !empty($model->deform_split)) {
                $minIds = $model->min_split . "；" . $model->deform_split;
            }
            if(empty($minIds))
                continue;

            $strokeSerial = implode('', $this->extractStrokeSerial($minIds, $model->radical));
            if (!empty($strokeSerial))
                $output[] = "{$model->id}\t{$model->word}\t{$model->pic_name}\t{$strokeSerial}";
            if (++$index > 1000) {
                $contents = implode("\r\n", $output) . "\r\n";
                file_put_contents('d:\Inbox\hanzi-set-serial.txt', $contents, FILE_APPEND);
                unset($output);
                $index = 1;
            }
        }

        $contents = implode("\r\n", $output). "\r\n";
        file_put_contents('d:\Inbox\hanzi-set-serial.txt', $contents, FILE_APPEND);

        echo "success!";
        die;
    }

    /**
     * 根据初步拆分递归混合拆分、最大拆分、拆分序列、结构等信息，生成sql文.
     * @return mixed
     */
    public function actionGenerateMixSplit()
    {
        mb_internal_encoding("UTF-8");
        mb_regex_encoding("UTF-8");

        $this->hanziIds = require_once('_unicodeMinSplitIds.php');

        $models = HanziSet::find()->orderBy('id')
            ->where("(min_split is not null or deform_split is not null) and mix_split is null")
            ->andWhere("id=242052")
            ->all();

        $index = 1;
        $outputWord = [];
        $outputIds = [];
        foreach ($models as $model) {
            // 从Split表获取人工拆字信息
//            $splitInfo = HanziSplit::getSplitInfo($model);
//            if (empty($splitInfo))
//                continue;
//            $model->min_split = $splitInfo['min_split'];
//            $model->deform_split = $splitInfo['deform_split'];
//            $model->similar_stock = $splitInfo['similar_stock'];

            // 递归生成混合拆分、最大拆分
            $minIds = $model->min_split . $model->deform_split;
            if(!empty($model->min_split) && !empty($model->deform_split)) {
                $minIds = $model->min_split . "；" . $model->deform_split;
            }
            if(empty($minIds))
                continue;

            $minIdsArr = explode("；", $minIds);
            if (empty($minIdsArr))
                continue;

            $structuresArr = [];
            $mixIdsArr = [];
            foreach ($minIdsArr as $minIds) {
                if (!empty($minIds)) {
                    $eachMixIds = $this->generateMixIds($minIds);
                    $mixIdsArr[] = str_replace('；', '&', $eachMixIds);
                    $structure = mb_substr($minIds, 0, 1, 'utf-8');
                    if (mb_strpos('⿰⿱⿴⿵⿶⿷󰃾⿸⿹⿺󰃿⿻', $structure) !== false) {
                        $structuresArr[] = $structure;
                    }
                }
            }

            $mixIds = implode("；", $mixIdsArr);
            $maxIds = mb_ereg_replace('<.>', '', $mixIds);
            $strokeSerial = implode('', $this->extractStrokeSerial($minIds, $model->radical));
            $structures = implode("", array_unique($structuresArr));

            $outputIds[] = "{$model->id}\t{$structures}\t{$mixIds}\t{$maxIds}\t{$strokeSerial}";

            $model->structure = $structures;
            $model->mix_split = $mixIds;
            $model->max_split = $maxIds;
            $model->stock_serial = $strokeSerial;
            $model->save();

            if (++$index > 5000) {
                $contents = implode("\r\n", $outputIds) . "\r\n";
                file_put_contents('d:\Inbox\hanzi-set-mix-max-ids.txt', $contents, FILE_APPEND);
                unset($output);
                $index = 1;
            }
        }

        file_put_contents('d:\Inbox\hanzi-set-mix-max-ids.txt', implode("\r\n", $outputIds), FILE_APPEND);
        file_put_contents('d:\Inbox\hanzi-set-word.txt', implode("\r\n", $outputWord));

        echo "success!";
        die;
    }

    /**
     * 根据最小拆分和部首，递归生成部件序列
     * @param $str
     */
    private function extractStrokeSerial($minIdsStr, $radical)
    {
        // 获取部件字频
        $totalChrCount = [];
        $verbMixIds = $this->getVerbMixIds($minIdsStr);

        foreach ($verbMixIds as $ids) {
            $eachChrCount = $this->getCharCount($ids);
            foreach ($eachChrCount as $chr => $count) {
                if (empty($totalChrCount[$chr]))
                    $totalChrCount[$chr] = $count;
                elseif ($totalChrCount[$chr] < $count)
                    $totalChrCount[$chr] = $count;
            }
        }

        // 将每个部首加入部件字频
        foreach (explode(';', $radical) as $r) {
            if (!empty($r) && !empty($totalChrCount[$r])) {
                $totalChrCount[$r] = 1;
            }
        }

        // 获取部件串
        $idsChar = '';
        foreach ($totalChrCount as $key => $value) {
            for ($i = 0; $i < $value; $i++) {
                $idsChar .= $key;
            }
        }

        // 排序
        $return = preg_split('/(?<!^)(?!$)/u', $idsChar);
        setlocale(LC_COLLATE, 'sk_SK.utf8');
        $f = function ($a, $b) {
            return strcoll($a, $b);
        };
        usort($return, $f);

        return $return;
    }


    /*
     * 从初步拆分递归生成完全混合拆分数组
     */
    private function getVerbMixIds($ids)
    {
        if (mb_strlen($ids, 'utf-8') == 1) {
            if (empty($this->hanziIds[$ids])) {
                return [$ids];
            } else {
                $idsArr = [];
                foreach ($this->getVerbMixIds($this->hanziIds[$ids]) as $i) {
                    $idsArr[] = "<$ids>{$i}";
                }
                return $idsArr;
            }
        } elseif (strpos($ids, '；') !== false) {
            $idsArr = [];
            foreach (explode('；', $ids) as $i) {
                $idsArr = array_merge($idsArr, $this->getVerbMixIds($i));
            }
            return $idsArr;
        } else {
            $items = preg_split('/(?<!^)(?!$)/u', $ids);
            $totalIds = [''];
            foreach ($items as $item) {
                $tempTotalIds = [];
                $eachArr = $this->getVerbMixIds($item);
                foreach ($totalIds as $ids) {
                    foreach ($eachArr as $each) {
                        $tempTotalIds[] = $ids . $each;
                    }
                }
                $totalIds = $tempTotalIds;
            }
            return $totalIds;
        }
    }

    /*
     * 从初步拆分递归得到部件字频
     */
    private function getCharCount($str)
    {
        $eachChrCount = [];
        // 删除空格、结构符、圈数字、分号等
        $chars = mb_ereg_replace('[\s\?&{}<>（）\[\]0-9a-zA-Z①-⑳⿰-⿻？；󰃾󰃿]', '', $str);
        foreach (preg_split('/(?<!^)(?!$)/u', $chars) as $chr) {
            if (empty($eachChrCount[$chr]))
                $eachChrCount[$chr] = 1;
            else
                $eachChrCount[$chr] = $eachChrCount[$chr] + 1;
        }
        return $eachChrCount;

    }

    /*
     * 从初步拆分递归生成混合拆分
     */
    private function generateMixIds($ids)
    {
        if (mb_strlen($ids, 'utf-8') == 1) {
            if (empty($this->hanziIds[$ids])) {
                return $ids;
            } else {
                return "<{$ids}>" . $this->generateMixIds($this->hanziIds[$ids]);
            }
        } else {
            $items = preg_split('/(?<!^)(?!$)/u', $ids);
            $totalIds = '';
            foreach ($items as $item) {
                $totalIds .= $this->generateMixIds($item);
            }
            return $totalIds;
        }
    }

    /**
     * 补充hanzi_set中未填写的部首和笔画信息.
     * @return mixed
     */
    public function actionStrokes()
    {
        $query = HanziSet::find()->where("type = 1 and pic_name is not null");
        $models = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]]
        ]);
        $models->pagination->pageSize = 50;

        return $this->render('strokes', [
            'models' => $models,
        ]);
    }


    /**
     * Lists all HanziParts models.
     * @return mixed
     */
    public function actionSave($id, $field)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $id = (int)trim($id);
        $model = HanziSet::findOne($id);
        if ($model == null) {
            return '{"status":"error", "msg": "data not found."}';
        }

        if (isset(Yii::$app->request->post()['value'])) {
            $value = Yii::$app->request->post()['value'];
            $field = '' . trim($field);
            if (strcmp($field, "ra") == 0) {
                $model->radical = trim($value);
            } elseif (strcmp($field, "ms") == 0) {
                $model->max_stroke = trim($value);
            }

            if ($model->save())
                return '{"status":"success", "id": "' . $model->id . '"}';
            else
                var_dump($model->getErrors());
        }

        return '{"status":"error", "msg": "uncertain."}';
    }


    /**
     * Lists all HanziSet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HanziSetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HanziSet model.
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
     * Creates a new HanziSet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HanziSet();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing HanziSet model.
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
     * Deletes an existing HanziSet model.
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
     * Finds the HanziSet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HanziSet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HanziSet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
