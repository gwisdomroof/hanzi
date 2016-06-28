<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\HanziSet;

/**
 * HanziSetSearch represents the model behind the search form about `common\models\HanziSet`.
 */
class HanziSetSearch extends HanziSet
{
    const SEARCH_WORD = 1;   # 查字
    const SEARCH_VARIANT = 2; # 查正异关系
    const SEARCH_REVERSE = 3; # 反查内部编码

    public $param;
    public $mode;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source', 'type', 'nor_var_type', 'frequence', 'duplicate', 'stocks', 'bhard', 'created_at', 'updated_at'], 'integer'],
            [['param'], 'trim'],
            [['word', 'pic_name', 'belong_standard_word_code', 'standard_word_code', 'position_code', 'duplicate_id', 'pinyin', 'radical', 'zhengma', 'wubi', 'structure', 'min_split', 'deform_split', 'similar_stock', 'max_split', 'mix_split', 'stock_serial', 'remark', 'mode'], 'safe'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = HanziSet::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'source' => $this->source,
            'type' => $this->type,
            'nor_var_type' => $this->nor_var_type,
            'duplicate' => $this->duplicate,
            'stocks' => $this->stocks,
            'bhard' => $this->bhard,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'word', $this->word])
            ->andFilterWhere(['like', 'pic_name', $this->pic_name])
            ->andFilterWhere(['like', 'belong_standard_word_code', $this->belong_standard_word_code])
            ->andFilterWhere(['like', 'standard_word_code', $this->standard_word_code])
            ->andFilterWhere(['like', 'position_code', $this->position_code])
            ->andFilterWhere(['like', 'duplicate_id', $this->duplicate_id])
            ->andFilterWhere(['like', 'pinyin', $this->pinyin])
            ->andFilterWhere(['like', 'radical', $this->radical])
            ->andFilterWhere(['like', 'zhengma', $this->zhengma])
            ->andFilterWhere(['like', 'wubi', $this->wubi])
            ->andFilterWhere(['like', 'structure', $this->structure])
            ->andFilterWhere(['like', 'min_split', $this->min_split])
            ->andFilterWhere(['like', 'deform_split', $this->deform_split])
            ->andFilterWhere(['like', 'similar_stock', $this->similar_stock])
            ->andFilterWhere(['like', 'max_split', $this->max_split])
            ->andFilterWhere(['like', 'mix_split', $this->mix_split])
            ->andFilterWhere(['like', 'stock_serial', $this->stock_serial])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }

    /**
     * 检索有三种方式：IDS，总笔画，相似部件
     * @param  [string] $params [检索表达式]
     * @return [integer]  $default     [缺省检索模式]
     */
    public function regSearch($params, $mode = self::SEARCH_WORD)
    {
        $params = trim($params);
        if (preg_match("/-w/", $params)) {
            $mode = self::SEARCH_WORD;
        } elseif (preg_match("/-v/", $params)) {
            $mode = self::SEARCH_VARIANT;
        } elseif (preg_match("/!/", $params)) {
            $mode = self::SEARCH_REVERSE;
        }

        $this->mode = $mode;

        switch ($mode) {
            case self::SEARCH_WORD:
              return $this->searchWord($params);
            case self::SEARCH_VARIANT:
              return $this->searchVariant($params);
            case self::SEARCH_REVERSE:
              return $this->searchReverse($params);
            default:
              return false;
        }
    }

    /**
     * 反向查找文字的内码
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function searchReverse($params)
    {
        // unicode(basic, a, b, c, d, e, compatiable) format
        $regUni = "/^\!([\x{4E00}-\x{9FD5}\x{3400}-\x{4DB5}\x{20000}-\x{2A6D6}\x{2A700}-\x{2B734}\x{2B740}-\x{2B81D}\x{2B820}-\x{2CEA1}\x{2F800}-\x{2FA1D}])$/u";

        // picture name format: taiwan, hanyu, gaoli
        $regTw = "/^\!([0-9a-z]{4,5}|[ABCN][0-9]{5}(-[0-9]{1,3})?)$/";
        $regHy = "/^\!([0-9]{1,4}n[0-9]{1,2})$/";
        $regGl = "/^\!([\x{4E00}-\x{9FD5}\x{3400}-\x{4DB5}\x{20000}-\x{2A6D6}\x{2A700}-\x{2B734}\x{2B740}-\x{2B81D}\x{2B820}-\x{2CEA1}\x{2F800}-\x{2FA1D}][12])$/u";

        $search = preg_replace('/\!/', '', $params);

        if (preg_match($regUni, $params, $matches)) {
            return HanziSet::find()->orderby('id')->where(['word' => $search, 'source' => HanziSet::SOURCE_UNICODE])->one();
        } elseif (preg_match($regTw, $params, $matches) || preg_match($regHy, $params, $matches) || preg_match($regGl, $params, $matches)) {
            return HanziSet::find()->orderby('id')->where(['pic_name' => $search])->one();
        } else {
            return false;
        }

    }

    /**
     * 正字查异体字，异体字查正字
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function searchVariant($params)
    {
        // unicode(basic, a, b, c, d, e, compatiable) format
        $regUni = "/^([\x{4E00}-\x{9FD5}\x{3400}-\x{4DB5}\x{20000}-\x{2A6D6}\x{2A700}-\x{2B734}\x{2B740}-\x{2B81D}\x{2B820}-\x{2CEA1}\x{2F800}-\x{2FA1D}])\s+(\-v)?$/u";

        // picture name format: taiwan, hanyu, gaoli
        $regTw = "/^([0-9a-z]{4,5}|[ABCN][0-9]{5}(-[0-9]{1,3})?)\s+(\-v)?$/";
        $regHy = "/^([0-9]{1,4}n[0-9]{1,2})\s+(\-v)?$/";
        $regGl = "/^([\x{4E00}-\x{9FD5}\x{3400}-\x{4DB5}\x{20000}-\x{2A6D6}\x{2A700}-\x{2B734}\x{2B740}-\x{2B81D}\x{2B820}-\x{2CEA1}\x{2F800}-\x{2FA1D}][12])\s+(\-v)?$/u";

        $variants = null;
        $normals = '';
        $search = preg_replace('/\s+(\-v)?/', '', $params);

        if (preg_match($regUni, $params, $matches)) {
            $models = HanziSet::find()->orderby('id')->where(['word' => $search])->all();
            $normals = $search;
            foreach ($models as $model) {
                if (!empty($model->belong_standard_word_code)) {
                    $normals .= str_replace(';', '', $model->belong_standard_word_code);
                }
            }
            $variants = HanziSet::find()->where("belong_standard_word_code ~ '[$normals]'")->all();
            
        } elseif (preg_match($regTw, $params, $matches) || preg_match($regHy, $params, $matches) || preg_match($regGl, $params, $matches)) {
            $models = HanziSet::find()->orderby('id')->where(['pic_name' => $search])->all();
            foreach ($models as $model) {
                if (!empty($model->belong_standard_word_code)) {
                    $normals .= str_replace(';', '', $model->belong_standard_word_code);
                }
            }
            $variants = HanziSet::find()->where("belong_standard_word_code ~ '[$normals]|$search'")->all();
        }

        $normalArr = preg_split('//u', $normals, -1, PREG_SPLIT_NO_EMPTY);
        $normalArr = array_unique($normalArr);
        $resArr = null;
        foreach ($variants as $variant) {
            foreach ($normalArr as $normal) {
                if (strpos($variant->belong_standard_word_code, $normal) !== false) {
                    $resArr[$variant->source][$normal][] = $variant;
                    continue;
                }
            }
        }

        return $resArr;
    }

    public function searchWord($params)
    {
        $sqlParam = '';
        $minStockNum = null;
        $maxStockNum = null;
        $restStockNum = null;
        $ids = '';  # IDS 序列
        $stocks = ''; # IDS 部件集
        $similarStock = []; # 相似部件集
        
        # 计算表示范围的剩余笔画
        $params = str_replace("，", ",", $params);
        preg_match_all('/(\d+)\s*,\s*(\d+)/', $params, $nums);
        if(!empty($nums[0])) {
            $minStockNum = (int)$nums[1][0];
            $maxStockNum = (int)$nums[2][0];
            $params = preg_replace('/(\d+)\s*,\s*(\d+)/', '', $params);
        }

        # 计算单独的剩余笔画 
        preg_match_all('/\d+/', $params, $nums);
        foreach ($nums[0] as $num) {
            $restStockNum += (int)$num;
        }
        $params = preg_replace('/\d+/', '', $params);

        # IDS 和 相似部件
        $components = preg_split('//u', $params, -1, PREG_SPLIT_NO_EMPTY);

        for ($i=0; $i < count($components); $i++) {
            if('~' == $components[$i]) {
                $similarStock[] .= $components[++$i];
            } else {
                $str = mb_ereg_replace("[①-⑳，？⿰-⿻0-9a-zA-Z]", "", $components[$i]);
                if(mb_strlen($str) == 0) {
                    $ids .= $components[$i];
                } else {
                    $ids .= $components[$i].".*";
                    $stocks .= ",'$components[$i]'";
                }
            }
        }

        // echo $stockNum.'|'.$minStockNum.'|'.$maxStockNum.'<br/>';
        // echo $stocks. '|' .$ids.'|'.$similarStock.'<br/>';

        # 笔画数
        if($minStockNum !== null || $maxStockNum !== null || $restStockNum !== null) {
            # 查询各部件笔画，累加得到部件总笔画
            if (!empty($stocks)) {
                $stockNumRows = HanziSet::find()->where("word in (" . substr($stocks, 1). ")" )->all();
                foreach ($stockNumRows as $item) {
                        $restStockNum += $item->stocks;
                }
            }

            # 计算总的剩余笔画范围
            $minStockNum += $restStockNum;
            $maxStockNum += $restStockNum;
            
            if($minStockNum == $maxStockNum) {
                $sqlParam .= "stocks = $minStockNum ";
            } else {
                $sqlParam .= "stocks >= $minStockNum AND stocks <= $maxStockNum ";
            }
        } 

        # 相似部件
        if (!empty($similarStock)) {
            sort($similarStock);
            $sStockParam = implode(".*", $similarStock);
            $sqlParam .= $sqlParam == '' ? "similar_stock ~ '$sStockParam' " : " AND similar_stock ~ '$sStockParam' ";
        }

        # IDS
        if($ids != '') {
            $sqlParam .= $sqlParam == '' ? "mix_split ~ '$ids'" : " AND mix_split ~ '$ids'";
        }

        # 去重
        $sqlParam .= $sqlParam == '' ? "duplicate = 0" : " AND duplicate = 0";

        return HanziSet::find()->where($sqlParam);

    }
    
}
