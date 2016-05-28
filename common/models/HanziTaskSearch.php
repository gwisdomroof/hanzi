<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\HanziTask;

/**
 * HanziTaskSearch represents the model behind the search form about `common\models\HanziTask`.
 */
class HanziTaskSearch extends HanziTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'leader_id', 'user_id', 'page', 'seq', 'start_id', 'end_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['remark'], 'safe'],
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
        $query = HanziTask::find();

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
            'leader_id' => $this->leader_id,
            'user_id' => $this->user_id,
            'page' => $this->page,
            'seq' => $this->seq,
            'start_id' => $this->start_id,
            'end_id' => $this->end_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
