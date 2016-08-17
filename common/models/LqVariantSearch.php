<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LqVariant;

/**
 * LqVariantSearch represents the model behind the search form about `common\models\LqVariant`.
 */
class LqVariantSearch extends LqVariant
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source', 'nor_var_type'], 'integer'],
            [['pic_name', 'variant_code', 'belong_standard_word_code'], 'safe'],
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
        $query = LqVariant::find();

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
            'source' => $this->source,
            'nor_var_type' => $this->nor_var_type,
        ]);

        $query->andFilterWhere(['like', 'pic_name', $this->pic_name])
            ->andFilterWhere(['like', 'variant_code', $this->variant_code])
            ->andFilterWhere(['like', 'belong_standard_word_code', $this->belong_standard_word_code]);

        return $dataProvider;
    }
}
