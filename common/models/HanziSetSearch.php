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
    const SEARCH_REVERSE = 2; # 反查内部编码

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
     * 部件笔画检字法
     * 可查字，或反查内部编码
     * @param  [string] $params [检索表达式]
     * @return [integer]  $default     [缺省检索模式]
     */
    public function bSearch($params, $mode = self::SEARCH_WORD)
    {
        $params = trim($params);
        if (preg_match("/-w/", $params)) {
            $mode = self::SEARCH_WORD;
        } elseif (preg_match("/!/", $params)) {
            $mode = self::SEARCH_REVERSE;
        }

        $this->mode = $mode;

        switch ($mode) {
            case self::SEARCH_WORD:
              return $this->searchWord($params);
            case self::SEARCH_REVERSE:
              return $this->searchReverse($params);
            default:
              return false;
        }
    }

    /**
     * 查正异关系
     * @param  [string] $params [检索表达式]
     * @return 
     */
    public function ySearch($param)
    {
        $param = trim($param);

        // unicode(basic, a, b, c, d, e, compatiable, gaoali-self-define) format
        $regUni = "/^[\x{4E00}-\x{9FD5}\x{3400}-\x{4DB5}\x{20000}-\x{2A6D6}\x{2A700}-\x{2B734}\x{2B740}-\x{2B81D}\x{2B820}-\x{2CEA1}\x{2F800}-\x{2FA1D}\x{E005}-\x{E3D6}]$/u";

        // picture name format: taiwan, hanyu, gaoli
        $regTw = "/^[0-9a-z]{4,5}|[ABCN][0-9]{5}(-[0-9]{1,3})?$/";
        $regHy = "/^[0-9]{1,4}n[0-9]{1,2}$/";
        $regGl = "/^[\x{4E00}-\x{9FD5}\x{3400}-\x{4DB5}\x{20000}-\x{2A6D6}\x{2A700}-\x{2B734}\x{2B740}-\x{2B81D}\x{2B820}-\x{2CEA1}\x{2F800}-\x{2FA1D}][12]$/u";
        
        $twNormals = '';    # 所属台湾正字
        $glNormals = '';    # 所属高丽正字
        $hyPosition = null;    # 所属汉语大字典位置
        $dhPosition = null;    # 所属敦煌俗字典位置
        $variants = [];     # 检索结果
        $result = null;     #返回结果
        if (preg_match($regUni, $param, $matches) || preg_match($regTw, $param, $matches) || preg_match($regHy, $param, $matches) || preg_match($regGl, $param, $matches)) {
            // 检索正字
            $search = $matches[0];
            $models = HanziSet::find()->orderBy('id')->orwhere(['word' => $search])->orwhere(['pic_name' => $search])->all();
            foreach ($models as $model) {
                switch ($model->source) {
                    case HanziSet::SOURCE_TAIWAN:
                        if ($model->nor_var_type == HanziSet::TYPE_NORMAL_PURE || $model->nor_var_type == HanziSet::TYPE_NORMAL_WIDE) {
                            $twNormals .= $model->word;
                        }
                        $twNormals .= str_replace([';','#'], '', $model->belong_standard_word_code);
                        break;
                    case HanziSet::SOURCE_GAOLI:
                        $glNormals .= str_replace(';', '', $model->belong_standard_word_code);
                        break;
                    case HanziSet::SOURCE_HANYU:
                        $hyPosition = $model->position_code;
                        break;
                    case HanziSet::SOURCE_DUNHUANG:
                        $dhPosition = $model->position_code;
                        break;
                }
            }

            // 查询异体字
            $query = HanziSet::find()->orderBy(['nor_var_type'=>SORT_ASC, 'id'=>SORT_ASC]);
            if (!empty($twNormals)) {
                $query->orFilterWhere(["and",
                    ["~", "belong_standard_word_code", "[$twNormals]|$search"],
                    ["!=", "nor_var_type", 0],
                    ["=", "source", HanziSet::SOURCE_TAIWAN]
                    ]); 
            }
            if (!empty($glNormals)) {
                $query->orFilterWhere(["and",
                    ["~", "belong_standard_word_code", "[$glNormals]|$search"],
                    ["!=", "nor_var_type", 0],
                    ["=", "source", HanziSet::SOURCE_GAOLI]
                    ]); 
            }
            
            if (empty($glNormals) && empty($twNormals)) {
                $query->andWhere("1!=1"); 
            }
            // echo $query->createCommand()->getRawSql(); die;
            $variants = $query->all();

            // 处理结果
            $normalArr = preg_split('//u', $twNormals . $glNormals, -1, PREG_SPLIT_NO_EMPTY);
            $normalArr = array_unique($normalArr);
            foreach ($variants as $variant) {
                foreach ($normalArr as $normal) {
                    if (strpos($variant->belong_standard_word_code, $normal) !== false) {
                        $result[$variant->source][$normal][] = $variant;
                        continue;
                    }
                }
            }
            if (!empty($hyPosition)) {
                $result[HanziSet::SOURCE_HANYU] = $hyPosition;
            }
            if (!empty($dhPosition)) {
                $result[HanziSet::SOURCE_DUNHUANG] = $dhPosition;
            }
        }
        return $result;
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
    
}
