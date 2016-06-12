<?php
namespace app\modules\yann\models;

use Yii;
use yii\data\Sort;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Comment extends ActiveRecord {
    public static function tableName() {
        return '{{%comment}}';
    }

    public function search($hash) {
        $query = Comment::find();
        $sort = new Sort([
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => [
                'created_at' => [
                    'desc' => ['created_at' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
            ],
        ]);
        $query->orderBy($sort->orders);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // if (!($this->load($params) && $this->validate())) {
        //     return $dataProvider;
        // }

        // $query->andFilterWhere([
        //     'hash'  => $this->hash,
        // ]);

        $query->andFilterWhere(['like', 'hash', $hash]);

        return $dataProvider;
    }
}