<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%hanzi}}".
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
class Hanzi extends \yii\db\ActiveRecord
{
    const SOURCE_UNICODE = 1;
    const SOURCE_TAIWAN = 2;
    const SOURCE_HANYU = 3;
    const SOURCE_GAOLI = 4;

    const TYPE_WORD = 1;
    const TYPE_PICTURE = 2;
    const TYPE_WORD_PICTURE = 3; 

    const TYPE_NORMAL_PURE = 0; # 纯正字
    const TYPE_VARIANT_NARROW = 1; # 狭义异体字
    const TYPE_NORMAL_WIDE = 2;  # 广义且正字
    const TYPE_VARIANT_WIDE = 3;    # 广义非正字

    const HARD_TRUE = 1;
    const HARD_FALSE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'hanzi_type', 'nor_var_type', 'stocks', 'hard10', 'hard20', 'hard30', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['word', 'radical', 'structure'], 'string', 'max' => 8],
            [['picture', 'corners', 'attach'], 'string', 'max' => 32],
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'source' => Yii::t('app', '来源'),
            'hanzi_type' => Yii::t('app', '类型'),
            'word' => Yii::t('app', '文字'),
            'picture' => Yii::t('app', '图片'),
            'nor_var_type' => Yii::t('app', '正异类型'),
            'standard_word' => Yii::t('app', '所属正字'),
            'position_code' => Yii::t('app', '位置'),
            'radical' => Yii::t('app', '部首'),
            'stocks' => Yii::t('app', '笔画'),
            'structure' => Yii::t('app', '结构'),
            'corners' => Yii::t('app', '四角'),
            'attach' => Yii::t('app', '附码'),
            'hard10' => Yii::t('app', '是否难字'),
            'initial_split11' => Yii::t('app', '初步拆分1'),
            'initial_split12' => Yii::t('app', '初步拆分2'),
            'deform_split10' => Yii::t('app', '调笔拆分'),
            'similar_stock10' => Yii::t('app', '相似部件'),
            'hard20' => Yii::t('app', '是否难字'),
            'initial_split21' => Yii::t('app', '初步拆分1'),
            'initial_split22' => Yii::t('app', '初步拆分2'),
            'deform_split20' => Yii::t('app', '调笔拆分'),
            'similar_stock20' => Yii::t('app', '相似部件'),
            'hard30' => Yii::t('app', '是否难字'),
            'initial_split31' => Yii::t('app', '初步拆分1'),
            'initial_split32' => Yii::t('app', '初步拆分2'),
            'deform_split30' => Yii::t('app', '调笔拆分'),
            'similar_stock30' => Yii::t('app', '相似部件'),
            'remark' => Yii::t('app', '备注'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '修改时间'),
        ];
    }

    /**
     * @inheritdoc
     * @return HanziQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HanziQuery(get_called_class());
    }

    /**
     * [getMaxSplitIdByPage description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function getIdRangeByPage($page)
    {
        $pageSize = Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false);

        $dataset = Hanzi::find()->orderBy('id')->where(['word' => ''])->offset($pageSize * ($page - 1))->limit($pageSize)->asArray()->all();

        return  [
            'minId' => (int)$dataset[0]['id'],
            'maxId' => (int)$dataset[count($dataset) - 1]['id']
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function norVarTypes()
    {
        return [
            self::TYPE_NORMAL_PURE => Yii::t('common', '纯正字'),
            self::TYPE_VARIANT_NARROW => Yii::t('common', '狭义异体字'),
            self::TYPE_NORMAL_WIDE => Yii::t('common', '广义且正字'),
            self::TYPE_VARIANT_WIDE => Yii::t('common', '广义非正字')
        ];
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
     * Returns user statuses list
     * @return array|mixed
     */
    public static function sources()
    {
        return [
            self::SOURCE_UNICODE => Yii::t('common', 'Unicode'),
            self::SOURCE_TAIWAN => Yii::t('common', '台湾异体字'),
            self::SOURCE_HANYU => Yii::t('common', '汉语大字典'),
            self::SOURCE_GAOLI => Yii::t('common', '高丽异体字')
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function hards()
    {
        return [
            self::HARD_TRUE => Yii::t('common', '是'),
            self::HARD_FALSE => Yii::t('common', '否'),
        ];
    }
}
