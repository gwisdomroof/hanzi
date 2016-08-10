<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziSet;
use common\models\HanziSetSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PHPExcel;
use PHPExcel\IOFactory;


/**
 * HanziController implements the CRUD actions for HanziSet model.
 */
class HanziSetController extends Controller
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
     * 异体字综合信息页面
     * @param  
     * $param 当前检索参数
     * $a 当前活动tab
     * @return mixed
     */
    public function actionVariant($param, $a='tw')
    {
        $models = HanziSet::find()->orderBy(['source' => SORT_ASC, 'id' => SORT_ASC])
            ->andFilterWhere(['or',
            ['word' => trim($param)],
            ['pic_name' => trim($param)]])
            ->all();

        // 概要信息
        $summary = array();
        $zitouArr = [];
        foreach ($models as $model) {
            switch ($model->source) {
                case HanziSet::SOURCE_TAIWAN:
                    $summary[$model->id]['source'] = HanziSet::sources()[$model->source];
                    # 所属正字
                    $standardWord = $model->belong_standard_word_code;
                    if ($model->nor_var_type == HanziSet::TYPE_NORMAL_WIDE) {
                        $standardWord =empty($model->word) ? $standardWord : $model->word . ';' . $standardWord;
                    }
                    # 原始位置
                    $position = '';
                    $posArr = explode(';', $model->position_code.';'.$model->standard_word_code);
                    $posArr = array_unique($posArr);
                    foreach ($posArr as $item) {
                        $item = ltrim($item, "#");
                        $position .= "<a class='tw' target='_blank' href='".$model->getOriginUrl($item)."' >$item</a>&nbsp;";
                    }
                    $summary[$model->id]['position']= $position;
                    $summary[$model->id]['standardWord']= $standardWord;
                    $summary[$model->id]['remark']= HanziSet::norVarTypes(false)[$model->nor_var_type];
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_TAIWAN;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }
                    break;

                case HanziSet::SOURCE_HANYU:
                    $summary[$model->id]['source'] = HanziSet::sources()[$model->source];
                    $summary[$model->id]['position'] = "<a class='hy' href='#' >$model->position_code</a>";
                    $summary[$model->id]['standardWord']= '';
                    $posArr = explode('-', $model->position_code);
                    $summary[$model->id]['remark']= '第' . $posArr[1] . '页 第' . $posArr[2] . '字';
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_HANYU;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }

                    break;

                case HanziSet::SOURCE_GAOLI:
                    $summary[$model->id]['source'] = HanziSet::sources()[$model->source];
                    $summary[$model->id]['position']= "<a class='gl' target='_blank' href='".$model->getOriginUrl($model->belong_standard_word_code)."'>$model->belong_standard_word_code</a>";
                    $summary[$model->id]['standardWord'] = "$model->belong_standard_word_code";
                    $summary[$model->id]['remark']= '';
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_GAOLI;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }
                    break;

                case HanziSet::SOURCE_DUNHUANG:
                    $summary[$model->id]['source'] = HanziSet::sources()[$model->source];
                    $summary[$model->id]['position']= "<a class='dh' href='#'>$model->position_code</a>";
                    $summary[$model->id]['standardWord'] = empty($model->word) ? $model->pic_name : $model->word;
                    $summary[$model->id]['remark']= "第".$model->position_code."页";
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_DUNHUANG;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }
                    break;

                case HanziSet::SOURCE_LONGQUAN:
                    # code...
                    break;
            }
        }

        // 页面信息
        $data = [];
        $index = 1;
        $active = 1;
        $sourceMap = ['tw'=>HanziSet::SOURCE_TAIWAN, 'hy'=>HanziSet::SOURCE_HANYU, 'gl'=>HanziSet::SOURCE_GAOLI, 'dh'=>HanziSet::SOURCE_DUNHUANG];
        $activeSource = $sourceMap[$a];
        foreach ($models as $model) {
            foreach ($model->getUrl() as $key => $url) {
                $data[$index]["id"] = "variant" . $index;
                $data[$index]["source"] = $model->source;
                $data[$index]["key"] = $key;
                $data[$index]["url"] = $url;
                if ($activeSource == $model->source && $active == 0) {
                    $active = $index;
                }
                $index++;
            }
        }

        return $this->render('variant', [
            'zitouArr' => $zitouArr,
            'summary' => $summary,
            'data' => $data,
            'active' => $active
        ]);
    }

    /**
     * 汉语大字典页面
     * @param $param 页码
     * @return mixed
     */
    public function actionHanyu($param)
    {
        $this->layout = '_clear';

        $maxPage = 5127;
        $page =  str_pad($param, 4, '0', STR_PAD_LEFT);
        return $this->render('hanyu', [
            'url' => "/img/hydzd/$page.png",
            'maxPage' => $maxPage,
        ]);
    }

    /**
     * 敦煌俗字典页面
     * @param $param 页码
     * @return mixed
     */
    public function actionDunhuang($param)
    {
        $this->layout = '_clear';
        
        $offset = 91;
        $param = trim($param) + 91;
        $page =  str_pad($param, 3, '0', STR_PAD_LEFT);
        $maxPage = 680;
        return $this->render('dunhuang', [
            'url' => "/img/dhszd/$page.png",
            'maxPage' => $maxPage,
        ]);
    }

    /**
     * 台湾异体字页面
     * @param $param 异体字对应的ID号，如“A00002-001”
     * @return mixed
     */
    public function actionTaiwan($param)
    {
        $this->layout = 'twyitizi';

        $param = trim($param);
        $positionArr = explode('-', $param);
        $normal = strtolower($positionArr[0]);
        $anchor = '';
        if (count($positionArr) > 1) {
            unset($positionArr[0]);
            $anchor = '#bm_' . implode($positionArr, '-');
        }

        $base = '/yitizi/';           
        $up = "";
        $down = "";
        $right = "";
        $fuluzi = json_decode(Yii::$app->get('keyStorage')->get('frontend.tw-fuluzi', null, false));

        $type = substr($normal, 0, 1);
        $up = $base . "yiti$type" . "/w$type" . "/w$normal.htm";
        $right = $base . "yiti$type" . "/s$type" . "/s$normal.htm";

        if (in_array($param, $fuluzi)) {
            $down = $base . "yiti$type" . "/fu$type" . "/fu$normal.htm";
        } elseif (!empty($anchor)) {
            $down = $base . "yiti$type" . "/yd$type" . "/yd$normal.htm" . $anchor;
        } else {
            $down = $base . "yiti$type" . "/$type" . "_std/$normal.htm";
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/html; charset=big5');
        
        return $this->render('taiwan', [
            'title' => $normal,
            'up' => $up,
            'down' => $down,
            'right' => $right
        ]);

    }


    /**
     * 高丽藏异体字页面
     * @param $param 异体字对应的ID号，可以是数据库中的word，pic_name，
     * belong_standard_word_code属性
     * @return mixed
     */
    public function actionGaoli($param)
    {
        $this->layout = '_clear';

        // 查param对应的正字
        $normals = HanziSet::find()->select('belong_standard_word_code')->where(['source' => HanziSet::SOURCE_GAOLI])->andWhere(['or',
                ['word' => trim($param)],
                ['pic_name' => trim($param)]
            ])->asArray()->all();

        // 查正字对应的所有异体字
        $models = HanziSet::find()->where(['source' => HanziSet::SOURCE_GAOLI, 'nor_var_type' => 1])
            ->andWhere(['belong_standard_word_code' => $normals])->all();

        return $this->render('gaoli', [
            'models' => $models,
        ]);
    }

    /**
     * 部件笔画检字法
     * @return mixed
     */
    public function actionBsearch()
    {
        $hanziSearch = new HanziSetSearch();

        # 如果是post请求，则转为Get请求
        if (Yii::$app->request->post()) {
            $hanziSearch->load(Yii::$app->request->post());
            $this->redirect(['bsearch', 'HanziSetSearch[param]' => $hanziSearch->param]);
        }
        
        $data = [];
        $pagination = new \yii\data\Pagination(['totalCount' => 0]);
        $message = null;

        if ($hanziSearch->load(Yii::$app->request->get()) && $hanziSearch->validate()) {
            $res = $hanziSearch->bSearch($hanziSearch->param);
            if ($hanziSearch->mode == HanziSetSearch::SEARCH_WORD) {
                $count = $res->count();
                $message = $count == 0 ? "查询结果为空。" : "共检索到".$count."条数据。";
                $pagination = new \yii\data\Pagination(['totalCount' => $count, 'pageSize' => 100]);
                $data = $res->orderBy('id')->offset($pagination->offset)->limit(100)->all();
            } elseif ($hanziSearch->mode == HanziSetSearch::SEARCH_REVERSE) {
                $data = $res;
            } 
        }

        return $this->render('bsearch', [
            'hanziSearch' => $hanziSearch,
            'data' => $data,
            'pagination' => $pagination,
            'message' => $message,
        ]);
    }


    /**
     * 异体字检索
     * @return mixed
     */
    public function actionYsearch()
    {
        $hanziSearch = new HanziSetSearch();

        # 如果是post请求，则转为Get请求
        if (Yii::$app->request->post()) {
            $hanziSearch->load(Yii::$app->request->post());
            $this->redirect(['ysearch', 'HanziSetSearch[param]' => $hanziSearch->param]);
        }

        $data = [];
        if ($hanziSearch->load(Yii::$app->request->get()) && $hanziSearch->validate()) {
            $data = $hanziSearch->ySearch($hanziSearch->param);
        }

        return $this->render('ysearch', [
            'hanziSearch' => $hanziSearch,
            'data' => $data,
            'param' => $hanziSearch->param,
        ]);
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
