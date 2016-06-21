<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\HanziHyyt;

/**
 * HanziHyytSearch represents the model behind the search form about `common\models\HanziHyyt`.
 */
class HanziHyytSearch extends HanziHyyt
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'page', 'num', 'type1', 'type2', 'type3', 'created_at', 'updated_at'], 'integer'],
            [['volume', 'picture', 'word1', 'tong_word1', 'zhushi1', 'word2', 'tong_word2', 'zhushi2', 'word3', 'tong_word3', 'zhushi3', 'remark'], 'safe'],
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
        $query = HanziHyyt::find()->orderBy('id');

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
            'page' => $this->page,
            'num' => $this->num,
            'type1' => $this->type1,
            'type2' => $this->type2,
            'type3' => $this->type3,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'volume', $this->volume])
            ->andFilterWhere(['like', 'picture', $this->picture])
            ->andFilterWhere(['like', 'word1', $this->word1])
            ->andFilterWhere(['like', 'tong_word1', $this->tong_word1])
            ->andFilterWhere(['like', 'zhushi1', $this->zhushi1])
            ->andFilterWhere(['like', 'word2', $this->word2])
            ->andFilterWhere(['like', 'tong_word2', $this->tong_word2])
            ->andFilterWhere(['like', 'zhushi2', $this->zhushi2])
            ->andFilterWhere(['like', 'word3', $this->word3])
            ->andFilterWhere(['like', 'tong_word3', $this->tong_word3])
            ->andFilterWhere(['like', 'zhushi3', $this->zhushi3])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
