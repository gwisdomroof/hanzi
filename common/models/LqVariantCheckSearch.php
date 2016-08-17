<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LqVariantCheck;

/**
 * LqVariantCheckSearch represents the model behind the search form about `common\models\LqVariantCheck`.
 */
class LqVariantCheckSearch extends LqVariantCheck
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source', 'nor_var_type1', 'nor_var_type2', 'bconfirm'], 'integer'],
            [['pic_name', 'variant_code1', 'belong_standard_word_code1', 'variant_code2', 'belong_standard_word_code2', 'remark'], 'safe'],
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
        $query = LqVariantCheck::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
            'sort'=> [
                'defaultOrder' => ['id'=>SORT_ASC]
            ]
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
            'source' => $this->source,
            'nor_var_type1' => $this->nor_var_type1,
            'nor_var_type2' => $this->nor_var_type2,
            'bconfirm' => $this->bconfirm,
        ]);

        $query->andFilterWhere(['like', 'pic_name', $this->pic_name])
            ->andFilterWhere(['like', 'variant_code1', $this->variant_code1])
            ->andFilterWhere(['like', 'belong_standard_word_code1', $this->belong_standard_word_code1])
            ->andFilterWhere(['like', 'variant_code2', $this->variant_code2])
            ->andFilterWhere(['like', 'belong_standard_word_code2', $this->belong_standard_word_code2])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
