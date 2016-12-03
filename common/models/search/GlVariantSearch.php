<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GlVariant;

/**
 * GltwDedupResultSearch represents the model behind the search form about `common\models\GltwDedupResult`.
 */
class GlVariantSearch extends GlVariant
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source', 'type', 'nor_var_type', 'created_at', 'updated_at'], 'integer'],
            [['word', 'pic_name', 'belong_standard_word_code', 'standard_word_code', 'position_code', 'duplicate_id1', 'duplicate_id2', 'duplicate_id3', 'remark'], 'safe'],
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
        $query = GlVariant::find();

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
            'type' => $this->type,
            'nor_var_type' => $this->nor_var_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'word', $this->word])
            ->andFilterWhere(['like', 'pic_name', $this->pic_name])
            ->andFilterWhere(['like', 'belong_standard_word_code', $this->belong_standard_word_code])
            ->andFilterWhere(['like', 'standard_word_code', $this->standard_word_code])
            ->andFilterWhere(['like', 'position_code', $this->position_code])
            ->andFilterWhere(['like', 'duplicate_id1', $this->duplicate_id1])
            ->andFilterWhere(['like', 'duplicate_id2', $this->duplicate_id2])
            ->andFilterWhere(['like', 'duplicate_id3', $this->duplicate_id3])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchDup($params)
    {
        $query = GlVariant::find()->orderBy('id')
            ->where("(duplicate_id1 != '' or duplicate_id2 != '') and 
            (duplicate_id1 != duplicate_id2 or duplicate_id1 is null or duplicate_id2 is null)");

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
            'type' => $this->type,
            'nor_var_type' => $this->nor_var_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'word', $this->word])
            ->andFilterWhere(['like', 'pic_name', $this->pic_name])
            ->andFilterWhere(['like', 'belong_standard_word_code', $this->belong_standard_word_code])
            ->andFilterWhere(['like', 'standard_word_code', $this->standard_word_code])
            ->andFilterWhere(['like', 'position_code', $this->position_code])
            ->andFilterWhere(['like', 'duplicate_id1', $this->duplicate_id1])
            ->andFilterWhere(['like', 'duplicate_id2', $this->duplicate_id2])
            ->andFilterWhere(['like', 'duplicate_id3', $this->duplicate_id3])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }

}
