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
    public $param;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source', 'type', 'nor_var_type', 'frequence', 'bduplicate', 'stocks', 'bhard', 'created_at', 'updated_at'], 'integer'],
            [['word', 'pic_name', 'belong_standard_word_code', 'standard_word_code', 'position_code', 'duplicate_id', 'pinyin', 'radical', 'zhengma', 'wubi', 'structure', 'min_split', 'deform_split', 'similar_stock', 'max_split', 'mix_split', 'stock_serial', 'remark'], 'safe'],
            [['param'], 'trim']
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
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'source' => $this->source,
            'type' => $this->type,
            'nor_var_type' => $this->nor_var_type,
            'bduplicate' => $this->bduplicate,
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
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function regSearch($params)
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
        $sqlParam .= $sqlParam == '' ? "bduplicate = 0" : " AND bduplicate = 0";

        return HanziSet::find()->where($sqlParam);

    }
    
}
