<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LqVariant;

/**
 * LqVariantSearch represents the model behind the search form about `common\models\LqVariant`.
 */
class LqVariantSearch extends LqVariant
{
    public $param;  # 异体字检索，查询参数

    /**
     * 正字查异体字
     *
     * @return array
     */
    public function vsearch()
    {
        # 查param对应的正字
        $param = trim($this->param);
        if (empty($param)) {
            return [];
        }
        $normals = $param;  # param对应正字，包括param本身
        $models = LqVariant::find()->orderBy('id')->where(['or', ['word' => $param], ['pic_name' => $param]])->all();
        foreach ($models as $model) {
            $normals .= str_replace(';', '', $model->belong_standard_word_code);
        }
        # 根据正字查异体字
        $data = [];
        $variants = LqVariant::find()->where(['~', 'belong_standard_word_code', "[$normals]"])
            ->orderBy(['nor_var_type'=>SORT_ASC, 'belong_standard_word_code'=>SORT_ASC])->all();
        foreach ($variants as $variant) {
            $standardWords = explode(';', $variant->belong_standard_word_code);
            foreach ($standardWords as $standardWord) {
                if (strpos($normals, $standardWord) !== false)
                    $data[$standardWord][] = $variant;
            }
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'source', 'type', 'nor_var_type', 'duplicate', 'frequence', 'bconfirm', 'stocks', 'bhard', 'created_at', 'updated_at'], 'integer'],
            [['word', 'pic_name', 'belong_standard_word_code', 'standard_word_code', 'position_code', 'duplicate_id', 'sutra_ids', 'pinyin', 'radical', 'zhengma', 'wubi', 'structure', 'min_split', 'deform_split', 'similar_stock', 'max_split', 'mix_split', 'stock_serial', 'remark'], 'safe'],
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
            'type' => $this->type,
            'nor_var_type' => $this->nor_var_type,
            'duplicate' => $this->duplicate,
            'frequence' => $this->frequence,
            'bconfirm' => $this->bconfirm,
            'stocks' => $this->stocks,
            'bhard' => $this->bhard,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'word', $this->word])
            ->andFilterWhere(['like', 'pic_name', $this->pic_name])
            ->andFilterWhere(['like', 'belong_standard_word_code', $this->belong_standard_word_code])
            ->andFilterWhere(['like', 'standard_word_code', $this->standard_word_code])
            ->andFilterWhere(['like', 'position_code', $this->position_code])
            ->andFilterWhere(['like', 'duplicate_id', $this->duplicate_id])
            ->andFilterWhere(['like', 'sutra_ids', $this->sutra_ids])
            ->andFilterWhere(['like', 'pinyin', $this->pinyin])
            ->andFilterWhere(['like', 'radical', $this->radical])
            ->andFilterWhere(['like', 'zhengma', $this->zhengma])
            ->andFilterWhere(['like', 'wubi', $this->wubi])
            ->andFilterWhere(['like', 'structure', $this->structure])
            ->andFilterWhere(['like', 'min_split', $this->min_split])
            ->andFilterWhere(['like', 'deform_split', $this->deform_split])
            ->andFilterWhere(['like', 'similar_stock', $this->similar_stock])
            ->andFilterWhere(['like', 'max_split', $this->max_split])
            ->andFilterWhere(['like', 'mix_split', $this->mix_split])
            ->andFilterWhere(['like', 'stock_serial', $this->stock_serial])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
