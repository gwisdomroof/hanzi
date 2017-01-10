<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\HanziParts;

/**
 * HanziPartsSearch represents the model behind the search form about `common\models\HanziParts`.
 */
class HanziPartsSearch extends HanziParts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'part_type', 'src_chs_lib', 'src_gb13000', 'src_old_lqhanzi', 'src_feijinchang', 'src_hujingyu', 'src_lqhanzi', 'lqhanzi_sn', 'is_redundant', 'frequency_zhzk', 'frequency', 'is_split_part', 'is_search_part', 'strokes', 'c_t', 'u_t'], 'integer'],
            [['part_char', 'part_pic_id', 'replace_parts', 'stroke_order', 'remark'], 'safe'],
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
        $query = HanziParts::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'part_type' => $this->part_type,
            'src_chs_lib' => $this->src_chs_lib,
            'src_gb13000' => $this->src_gb13000,
            'src_old_lqhanzi' => $this->src_old_lqhanzi,
            'src_feijinchang' => $this->src_feijinchang,
            'src_hujingyu' => $this->src_hujingyu,
            'src_lqhanzi' => $this->src_lqhanzi,
            'lqhanzi_sn' => $this->lqhanzi_sn,
            'is_redundant' => $this->is_redundant,
            'frequency_zhzk' => $this->frequency_zhzk,
            'frequency' => $this->frequency,
            'is_split_part' => $this->is_split_part,
            'is_search_part' => $this->is_search_part,
            'strokes' => $this->strokes,
            'c_t' => $this->c_t,
            'u_t' => $this->u_t,
        ]);

        $query->andFilterWhere(['like', 'part_char', $this->part_char])
            ->andFilterWhere(['like', 'part_pic_id', $this->part_pic_id])
            ->andFilterWhere(['like', 'replace_parts', $this->replace_parts])
            ->andFilterWhere(['like', 'stroke_order', $this->stroke_order])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
