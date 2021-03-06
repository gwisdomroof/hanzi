<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%hanzi_split}}".
 *
 * @property string $id
 * @property integer $source
 * @property integer $hanzi_type
 * @property string $word
 * @property string $picture
 * @property integer $nor_var_type
 * @property string $standard_word
 * @property string $position_code
 * @property string $radical
 * @property integer $stocks
 * @property string $structure
 * @property string $corners
 * @property string $attach
 * @property integer $hard10
 * @property string $initial_split11
 * @property string $initial_split12
 * @property string $deform_split10
 * @property string $similar_stock10
 * @property integer $hard20
 * @property string $initial_split21
 * @property string $initial_split22
 * @property string $deform_split20
 * @property string $similar_stock20
 * @property integer $hard30
 * @property string $initial_split31
 * @property string $initial_split32
 * @property string $deform_split30
 * @property string $similar_stock30
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class HanziSplit extends \yii\db\ActiveRecord
{
    const TYPE_WORD = 1;
    const TYPE_PICTURE = 2;
    const TYPE_WORD_PICTURE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_split}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'hanzi_type', 'nor_var_type', 'stocks', 'duplicate', 'hard10', 'hard20', 'hard30', 'created_at', 'updated_at', 'split20_completed', 'split30_completed'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['word', 'radical', 'structure'], 'string', 'max' => 8],
            [['picture', 'corners', 'attach', 'duplicate10', 'duplicate20', 'duplicate30',], 'string', 'max' => 32],
            [['standard_word', 'position_code'], 'string', 'max' => 64],
            [['initial_split11', 'initial_split12', 'deform_split10', 'similar_stock10', 'initial_split21', 'initial_split22', 'deform_split20', 'similar_stock20', 'initial_split31', 'initial_split32', 'deform_split30', 'similar_stock30', 'remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function nextSplitId($curId)
    {
        if (!isset($curId))
            return false;

        $query = HanziSplit::find()->orderBy('id')->andWhere('id > :id', [':id' => $curId])->andWhere(['duplicate' => 0]);

        return $query->one()->id;

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'source' => Yii::t('common', '来源'),
            'hanzi_type' => Yii::t('common', '类型'),
            'word' => Yii::t('common', '文字'),
            'picture' => Yii::t('common', '图片'),
            'nor_var_type' => Yii::t('common', '正异类型'),
            'standard_word' => Yii::t('common', '所属正字'),
            'position_code' => Yii::t('common', '位置'),
            'radical' => Yii::t('common', '部首'),
            'stocks' => Yii::t('common', '笔画'),
            'structure' => Yii::t('common', '结构'),
            'corners' => Yii::t('common', '四角'),
            'attach' => Yii::t('common', '附码'),
            'duplicate10' => Yii::t('common', '重复值'),
            'hard10' => Yii::t('common', '是否难字'),
            'initial_split11' => Yii::t('common', '初步拆分1'),
            'initial_split12' => Yii::t('common', '初步拆分2'),
            'deform_split10' => Yii::t('common', '调笔拆分'),
            'similar_stock10' => Yii::t('common', '相似部件'),
            'duplicate20' => Yii::t('common', '重复值'),
            'hard20' => Yii::t('common', '是否难字'),
            'initial_split21' => Yii::t('common', '初步拆分1'),
            'initial_split22' => Yii::t('common', '初步拆分2'),
            'deform_split20' => Yii::t('common', '调笔拆分'),
            'similar_stock20' => Yii::t('common', '相似部件'),
            'duplicate30' => Yii::t('common', '重复值'),
            'hard30' => Yii::t('common', '是否难字'),
            'initial_split31' => Yii::t('common', '初步拆分1'),
            'initial_split32' => Yii::t('common', '初步拆分2'),
            'deform_split30' => Yii::t('common', '调笔拆分'),
            'similar_stock30' => Yii::t('common', '相似部件'),
            'remark' => Yii::t('common', '备注'),
            'created_at' => Yii::t('common', '创建时间'),
            'updated_at' => Yii::t('common', '修改时间'),
        ];
    }

    /**
     * [getMaxSplitIdByPage description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function getUnfinishedMinId($startId, $endId, $seq)
    {

        $query = HanziSplit::find()
            ->where(['duplicate' => 0])
            ->andWhere(['>=', 'id', $startId])
            ->andWhere(['<=', 'id', $endId])
            ->orderBy('id');

        // 初次拆分时，加一个临时字段；回查阶段可以去掉
        $query->andWhere('is_duplicated_temp is null or is_duplicated_temp = 0');
        if ($seq == 1) {
            // updated_at为空表示尚未作更新，即尚未进行初次拆分
            $query->andWhere('updated_at is null or updated_at = created_at');
        } elseif ($seq == 2) {
            $query->andWhere('split20_completed = 0 or split20_completed is null');
        } elseif ($seq == 3) {
            $query->andWhere('split30_completed = 0 or split30_completed is null');
        }
        $model = $query->one();
        return empty($model) ? false : $model->id;
    }

    /**
     * [getMaxSplitIdByPage description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function getIdRangeByPage($page)
    {
        $pageSize = Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false);

        $dataset = HanziSplit::find()->where(['duplicate' => 0])
            ->offset($pageSize * ($page - 1))->limit($pageSize)
            ->orderBy('id')->asArray()->all();

        // 处理is_duplicated_temp，新增一段代码
        $maxId = 0;
        for ($i = count($dataset) - 1; $i >= 0; $i--) {
            if (empty($dataset[$i]['is_duplicated_temp'])) {
                $maxId = (int)$dataset[$i]['id'];
                break;
            }
        }

        return [
            'minId' => (int)$dataset[0]['id'],
            'maxId' => $maxId
        ];
    }

    /**
     * [getMaxSplitIdByPage description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public function isNew($seq)
    {
        if (!empty($this->getAttribute("duplicate$seq" . "0"))
            || !empty($this->getAttribute("initial_split$seq" . "1"))
            || !empty($this->getAttribute("initial_split$seq" . "2"))
            || !empty($this->getAttribute("deform_split$seq" . "0"))
            || !empty($this->getAttribute("similar_stock$seq" . "0"))
        ) {
            return false;
        }
        return true;
    }

    /**
     * [getMaxSplitIdByPage description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public function loadFromFirstSplit()
    {
        $this->duplicate20 = $this->duplicate10;
        $this->hard20 = $this->hard10;
        $this->initial_split21 = $this->initial_split11;
        $this->initial_split22 = $this->initial_split12;
        $this->deform_split20 = $this->deform_split10;
        $this->similar_stock20 = $this->similar_stock10;
    }


    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function types()
    {
        return [
            self::TYPE_WORD => Yii::t('common', '文字'),
            self::TYPE_PICTURE => Yii::t('common', '图片'),
            self::TYPE_WORD_PICTURE => Yii::t('common', '文字且图片')
        ];
    }

    /**
     * 如果id>=98240，即拆字页面1022页，则表示进入高丽异体字拆字页面
     * @return array|mixed
     */
    public static function getSplitTaskType($id)
    {
        return $id >= 98240 ? HanziTask::TYPE_GAOLI_SPLIT : HanziTask::TYPE_SPLIT;
    }


    /**
     * 获取pic_name的拆字信息：重复值，初步拆分，调笔拆分和相似部件
     * @param $pic_name
     */
    public static function getSplitInfo($hanzi)
    {
        $picName = str_replace('kr', '', $hanzi->pic_name);
        $model = HanziSplit::find()->where("picture = '{$picName}'")->one();
        if (empty($model))
            return false;

        $output = [];
        $minSplitIds = empty($hanzi->min_split) ? [] : explode('；', $hanzi->min_split);
        $deforamSplitIds = empty($hanzi->deform_split) ? [] : explode('；', $hanzi->deform_split);
        $similarParts = $model->duplicate10 . $model->duplicate20 . $hanzi->similar_stock;

        if (!empty($model->initial_split11))
            $minSplitIds[] = trim($model->initial_split11);
        if (!empty($model->initial_split12))
            $minSplitIds[] = trim($model->initial_split12);
        if (!empty($model->deform_split10))
            $deforamSplitIds[] = trim($model->deform_split10);
        if (!empty($model->similar_stock10))
            $similarParts .= trim($model->similar_stock10);

        if (!empty($model->initial_split21))
            $minSplitIds[] = trim($model->initial_split21);
        if (!empty($model->initial_split22))
            $minSplitIds[] = trim($model->initial_split22);
        if (!empty($model->deform_split20))
            $deforamSplitIds[] = trim($model->deform_split20);
        if (!empty($model->similar_stock20))
            $similarParts .= trim($model->similar_stock20);

        $minSplitIds = array_unique($minSplitIds);
        $minSplitIds = implode('；', $minSplitIds);

        $deforamSplitIds = array_unique($deforamSplitIds);
        $deforamSplitIds = implode('；', $deforamSplitIds);

        $similarParts = array_unique(preg_split('/(?<!^)(?!$)/u', $similarParts));
        setlocale(LC_COLLATE, 'sk_SK.utf8');
        $f = function ($a, $b) {
            return strcoll($a, $b);
        };
        usort($similarParts, $f);
        $similarParts = implode('', $similarParts);

//        $output = "{$model->id}\t{$model->picture}\t{$minSplitIds}\t{$deforamSplitIds}\t{$similarParts}";

        return [
            'word' => $model->word,
            'min_split' => $minSplitIds,
            'deform_split' => $deforamSplitIds,
            'similar_stock' => $similarParts
        ];

    }

    /**
     * [getMaxSplitIdByPage description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function getLeakHanzi()
    {
        $query = HanziSplit::find()->orderBy('id');

        $query->where('(duplicate = 0 or duplicate is null ) and (is_duplicated_temp = 0 or is_duplicated_temp is null) 
            and (initial_split21 is null or initial_split21 = \'\') and (initial_split22 is null or initial_split22 = \'\') 
            and (deform_split20 is null or deform_split20 = \'\') and (duplicate20 is null or duplicate20 = \'\')
        ');
        $model = $query->one();
        if (!empty($model)) {
            return ['seq' => 2, 'id' => $model->id];
        }

        $query->where('(duplicate = 0 or duplicate is null ) and (is_duplicated_temp = 0 or is_duplicated_temp is null) 
            and (initial_split11 is null or initial_split11 = \'\') and (initial_split12 is null or initial_split12 = \'\') 
            and (deform_split10 is null or deform_split10 = \'\') and (duplicate10 is null or duplicate10 = \'\')
        ');
        $model = $query->one();
        if (!empty($model)) {
            return ['seq' => 1, 'id' => $model->id];
        }

        return false;
    }

}
