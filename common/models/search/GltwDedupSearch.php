<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GltwDedup;

/**
 * GltwDedupSearch represents the model behind the search form about `common\models\GltwDedup`.
 */
class GltwDedupSearch extends GltwDedup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'relation', 'created_at', 'updated_at'], 'integer'],
            [['gaoli', 'unicode', 'remark'], 'safe'],
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
    public function search($params, $excerptNoNeed = true)
    {
        $query = GltwDedup::find()->orderBy(['relation' => SORT_ASC]);

        // add conditions that should always apply here
        if ($excerptNoNeed)
            $query->andWhere(['!=', 'status', GltwDedup::STATUS_NONEED]);

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
            'status' => $this->status,
            'relation' => $this->relation,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'gaoli', $this->gaoli])
            ->andFilterWhere(['like', 'unicode', $this->unicode])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
