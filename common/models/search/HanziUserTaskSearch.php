<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\HanziUserTask;

/**
 * HanziUserTaskSearch represents the model behind the search form about `common\models\HanziUserTask`.
 */
class HanziUserTaskSearch extends HanziUserTask
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userid', 'taskid', 'task_type', 'task_seq', 'task_status', 'quality', 'created_at', 'updated_at', 'cnt'], 'integer'],
            [['user.username'], 'safe'],
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

    public function attributes()
    {
        // 添加关联字段到可搜索特性
        return array_merge(parent::attributes(), ['cnt']);
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
        $query = HanziUserTask::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        // 转换时间格式
        $updateTime = $this->updated_at;
        unset($this->updated_at);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'userid' => $this->userid,
            'taskid' => $this->taskid,
            'task_type' => $this->task_type,
            'task_seq' => $this->task_seq,
            'task_status' => $this->task_status,
            'quality' => $this->quality,
            'created_at' => $this->created_at,
        ]);

        if (!empty($updateTime)) {
            list($y, $m, $d) = explode('-', $updateTime);
            $beginTime = mktime(0, 0, 0, $m, $d, $y);
            $endTime = mktime(23, 59, 59, $m, $d, $y);
            $query->andFilterWhere(['and', ['>=', 'hanzi_user_task.updated_at', $beginTime], ['<=', 'hanzi_user_task.updated_at', $endTime]]);
        }

        $query->joinWith(['user']);
        $query->andFilterWhere(['like', 'user.username', $this->getAttribute('user.username')]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     */
    public function dailySearch($params)
    {
        $query = HanziUserTask::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (empty($params['HanziUserTaskSearch'])) {
            return $dataProvider;
        }

        $query->select(["userid, \"user\".username, count(taskid) AS cnt"])->groupBy(['userid', 'username']);

        $query->joinWith(['user']);

        if (isset($params['HanziUserTaskSearch']['task_type']))
            $query->andFilterWhere(['task_type' => $params['HanziUserTaskSearch']['task_type']]);

        if (isset($params['HanziUserTaskSearch']['updated_at']) && !empty($params['HanziUserTaskSearch']['updated_at'])) {
            $this->updated_at = $params['HanziUserTaskSearch']['updated_at'];
            if (!empty($this->updated_at)) {
                list($y, $m, $d) = explode('-', $this->updated_at);
                $beginTime = mktime(0, 0, 0, $m, $d, $y);
                $endTime = mktime(23, 59, 59, $m, $d, $y);
                $query->andFilterWhere(['and', ['>=', 'hanzi_user_task.updated_at', $beginTime], ['<=', 'hanzi_user_task.updated_at', $endTime]]);
            }
        }

        $query->andFilterWhere(['like', 'user.username', $this->getAttribute('user.username')]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function countScores($params)
    {
        $query = HanziUserTask::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cnt' => SORT_DESC]]
        ]);

        if (isset($params['HanziUserTaskSearch']['task_type']) && $params['HanziUserTaskSearch']['task_type'] !== '0') {
            $query->select(['userid, task_type, SUM(quality) AS cnt'])->groupBy(['userid', 'task_type']);
            $this->load($params);
            $query->andFilterWhere(['task_type' => $this->task_type]);
        } else {
            $query->select(['userid, SUM(quality) AS cnt'])->groupBy(['userid']);
            // unset($params['HanziUserTaskSearch']['task_type']);
            $this->load($params);
        }

        $query->joinWith(['user']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->cnt)) {
            $query->having(['SUM(quality)' => $this->cnt]);
        }

        $query->andFilterWhere(['like', 'user.username', $this->getAttribute('user.username')]);

        return $dataProvider;
    }

}
