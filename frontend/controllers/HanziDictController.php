<?php

namespace frontend\controllers;

use common\models\HanziSet;
use common\models\search\HanziSetSearch;
use common\models\search\LqVariantSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * HanziController implements the CRUD actions for HanziSet model.
 */
class HanziDictController extends Controller
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
     * 异体字检索
     * @return mixed
     */
    public function actionSearch()
    {
        # 将post请求转为Get请求，以免get请求中的参数累加
        if (Yii::$app->request->post()) {
            $this->redirect(['search', 'param' => Yii::$app->request->post()['HanziSetSearch']['param']]);
        }

        $param = trim(Yii::$app->request->get('param'));
        $hanziSearch = new HanziSetSearch();
        $hanziSearch->param = $param;
        $lqVariantSearch = new LqVariantSearch();
        $lqVariantSearch->param = $param;
        $hanziSet = [];
        $lqVariants = [];
        if ($hanziSearch->validate() && $lqVariantSearch->validate()) {
            $hanziSet = $hanziSearch->vsearch();
            $lqVariants = $lqVariantSearch->vsearch();
        }

        return $this->render('search', [
            'hanziSearch' => $hanziSearch,
            'hanziSet' => $hanziSet,
            'lqVariants' => $lqVariants,
            'param' => $param,
        ]);
    }

    /**
     * 异体字检索，提供给异体字判取使用
     * @return mixed
     */
    public function actionMsearch()
    {
        $this->layout = '_clear';
        # 将post请求转为Get请求，以免get请求中的参数累加
        if (Yii::$app->request->post()) {
            $this->redirect(['msearch', 'param' => Yii::$app->request->post()['HanziSetSearch']['param']]);
        }

        $param = trim(Yii::$app->request->get('param'));
        $hanziSearch = new HanziSetSearch();
        $hanziSearch->param = $param;
        $lqVariantSearch = new LqVariantSearch();
        $lqVariantSearch->param = $param;
        $hanziSet = [];
        $lqVariants = [];
        if ($hanziSearch->validate() && $lqVariantSearch->validate()) {
            $hanziSet = $hanziSearch->vsearch();
            $lqVariants = $lqVariantSearch->vsearch();
        }

        return $this->render('msearch', [
            'hanziSearch' => $hanziSearch,
            'hanziSet' => $hanziSet,
            'lqVariants' => $lqVariants,
            'param' => $param,
        ]);

    }

    /**
     * 异体字综合信息页面
     * @param
     * $param 当前检索参数
     * $a 当前活动tab
     * @return mixed
     */
    public function actionVariant($param, $a = 'tw')
    {
        $param = trim($param);
        $query = HanziSet::find()->orderBy(['source' => SORT_ASC, 'id' => SORT_ASC])
            ->where(['and', 'source > 1', ['or',
                ['=', 'word', $param],
                ['=', 'pic_name', $param],
                ['~', 'position_code', "{$param}(;|$)"],
                ['~', 'duplicate_id', "{$param}(;|$)"],
                ['~', 'korean_dup_hanzi', "{$param}(;|$)"]
            ]]);
        $models = $query->all();

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
                        $standardWord = empty($model->word) ? $standardWord : $model->word . ';' . $standardWord;
                    }
                    # 原始位置
                    $position = '';
                    $posArr = explode(';', $model->position_code . ';' . $model->standard_word_code);
                    $posArr = array_unique($posArr);
                    foreach ($posArr as $item) {
                        $item = ltrim($item, "#");
                        $position .= "<a class='tw' target='_blank' href='" . $model->getOriginUrl($item) . "' >$item</a>&nbsp;";
                    }
                    $summary[$model->id]['position'] = $position;
                    $summary[$model->id]['standardWord'] = $standardWord;
                    $summary[$model->id]['remark'] = HanziSet::norVarTypes(false)[$model->nor_var_type];
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_TAIWAN;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }
                    break;

                case HanziSet::SOURCE_HANYU:
                    $summary[$model->id]['source'] = HanziSet::sources()[$model->source];
                    $summary[$model->id]['position'] = "<a class='hy' href='#' >$model->position_code</a>";
                    $summary[$model->id]['standardWord'] = '';
                    $posArr = explode('-', $model->position_code);
                    $summary[$model->id]['remark'] = '第' . $posArr[0] . '页 第' . $posArr[1] . '字';
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_HANYU;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }

                    break;

                case HanziSet::SOURCE_GAOLI:
                    $summary[$model->id]['source'] = HanziSet::sources()[$model->source];
                    $summary[$model->id]['position'] = "<a class='gl' target='_blank' href='" . $model->getOriginUrl($model->belong_standard_word_code) . "'>$model->belong_standard_word_code</a>";
                    $summary[$model->id]['standardWord'] = "$model->belong_standard_word_code";
                    $summary[$model->id]['remark'] = '';
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_GAOLI;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }
                    break;

                case HanziSet::SOURCE_DUNHUANG:
                    $summary[$model->id]['source'] = HanziSet::sources()[$model->source];
                    $summary[$model->id]['position'] = "<a class='dh' href='#'>$model->position_code</a>";
                    $summary[$model->id]['standardWord'] = empty($model->word) ? $model->pic_name : $model->word;
                    $summary[$model->id]['remark'] = "第" . $model->position_code . "页";
                    if (empty($zitouArr)) {
                        $zitouArr['source'] = HanziSet::SOURCE_DUNHUANG;
                        $zitouArr['zitou'] = !empty($model->word) ? $model->word : $model->pic_name;
                    }
                    break;

                case HanziSet::SOURCE_OTHER:
                    # code...
                    break;
            }
        }

        // 页面信息
        $data = [];
        $index = 1;
        $active = 1;
        $sourceMap = ['tw' => HanziSet::SOURCE_TAIWAN, 'hy' => HanziSet::SOURCE_HANYU, 'gl' => HanziSet::SOURCE_GAOLI, 'dh' => HanziSet::SOURCE_DUNHUANG];
        $activeSource = $sourceMap[$a];
        foreach ($models as $model) {
            foreach ($model->getUrl() as $key => $url) {
                $data[$index]["id"] = "variant" . $index;
                $data[$index]["source"] = $model->source;
                $data[$index]["key"] = $key;
                $data[$index]["url"] = $url;
                if ($activeSource == $model->source) {
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
        $page = str_pad($param, 4, '0', STR_PAD_LEFT);
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
        $param = trim($param);
        $page = str_pad($param, 3, '0', STR_PAD_LEFT);
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
        $model = HanziSet::find()->where("pic_name = '{$param}' or position_code ~ '{$param}($|;)'")->one();
        $param = explode(';', $model->position_code)[0];

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
        $normals = HanziSet::find()
            ->select('belong_standard_word_code')
            ->where(['source' => HanziSet::SOURCE_GAOLI])
            ->andWhere(['or',
                ['word' => trim($param)],
                ['pic_name' => trim($param)]
            ])
            ->asArray()
            ->all();

        // 查正字对应的所有异体字
        $models = HanziSet::find()->orderBy('belong_standard_word_code')
            ->where(['source' => HanziSet::SOURCE_GAOLI])
            ->andWhere('nor_var_type > 0')
            ->andWhere(['belong_standard_word_code' => $normals])
            ->all();

        return $this->render('gaoli', [
            'models' => $models,
            'param' => $param
        ]);
    }

}
