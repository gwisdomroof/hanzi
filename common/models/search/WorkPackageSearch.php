<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WorkPackage;

/**
 * WorkPackageSearch represents the model behind the search form about `common\models\WorkPackage`.
 */
class WorkPackageSearch extends WorkPackage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userid', 'type', 'volume', 'daily_schedule', 'expected_date', 'progress', 'created_at', 'updated_at'], 'integer'],
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
        $query = WorkPackage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!empty($params['progress']) && $params['progress'] == 'finished') {
            $query->where('progress >= volume');
        } elseif (!empty($params['progress']) && $params['progress'] == 'ongoing') {
            $query->where('progress < volume');
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'userid' => $this->userid,
            'type' => $this->type,
            'volume' => $this->volume,
            'daily_schedule' => $this->daily_schedule,
            'expected_date' => $this->expected_date,
            'progress' => $this->progress,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
