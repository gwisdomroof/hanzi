<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Hanzi;

/**
 * HanziSearch represents the model behind the search form about `common\models\Hanzi`.
 */
class HanziSearch extends Hanzi
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source', 'hanzi_type', 'nor_var_type', 'stocks', 'hard10', 'hard20', 'hard30', 'created_at', 'updated_at'], 'integer'],
            [['word', 'picture', 'standard_word', 'position_code', 'radical', 'structure', 'corners', 'attach', 'initial_split11', 'initial_split12', 'deform_split10', 'similar_stock10', 'initial_split21', 'initial_split22', 'deform_split20', 'similar_stock20', 'initial_split31', 'initial_split32', 'deform_split30', 'similar_stock30', 'remark'], 'safe'],
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
        $query = Hanzi::find()->where(['word' => '']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_ASC]]
        ]);


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'source' => $this->source,
            'hanzi_type' => $this->hanzi_type,
            'nor_var_type' => $this->nor_var_type,
            'stocks' => $this->stocks,
            'hard10' => $this->hard10,
            'hard20' => $this->hard20,
            'hard30' => $this->hard30,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'word', $this->word])
            ->andFilterWhere(['like', 'picture', $this->picture])
            ->andFilterWhere(['like', 'standard_word', $this->standard_word])
            ->andFilterWhere(['like', 'position_code', $this->position_code])
            ->andFilterWhere(['like', 'radical', $this->radical])
            ->andFilterWhere(['like', 'structure', $this->structure])
            ->andFilterWhere(['like', 'corners', $this->corners])
            ->andFilterWhere(['like', 'attach', $this->attach])
            ->andFilterWhere(['like', 'initial_split11', $this->initial_split11])
            ->andFilterWhere(['like', 'initial_split12', $this->initial_split12])
            ->andFilterWhere(['like', 'deform_split10', $this->deform_split10])
            ->andFilterWhere(['like', 'similar_stock10', $this->similar_stock10])
            ->andFilterWhere(['like', 'initial_split21', $this->initial_split21])
            ->andFilterWhere(['like', 'initial_split22', $this->initial_split22])
            ->andFilterWhere(['like', 'deform_split20', $this->deform_split20])
            ->andFilterWhere(['like', 'similar_stock20', $this->similar_stock20])
            ->andFilterWhere(['like', 'initial_split31', $this->initial_split31])
            ->andFilterWhere(['like', 'initial_split32', $this->initial_split32])
            ->andFilterWhere(['like', 'deform_split30', $this->deform_split30])
            ->andFilterWhere(['like', 'similar_stock30', $this->similar_stock30])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
