<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%hanzi_set}}".
 *
 * @property string $id
 * @property integer $source
 * @property integer $type
 * @property string $word
 * @property string $pic_name
 * @property integer $nor_var_type
 * @property string $belong_standard_word_code
 * @property string $standard_word_code
 * @property string $position_code
 * @property integer $bduplicate
 * @property string $duplicate_id
 * @property integer $frequency
 * @property string $pinyin
 * @property string $radical
 * @property integer $stocks
 * @property string $zhengma
 * @property string $wubi
 * @property string $structure
 * @property integer $bhard
 * @property string $min_split
 * @property string $deform_split
 * @property string $similar_stock
 * @property string $max_split
 * @property string $mix_split
 * @property string $stock_serial
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class HanziSet extends \yii\db\ActiveRecord
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
        return '{{%hanzi_set}}';
    }

    /**
     * @inheritdoc
     */
    public static function getPicturePath($soure, $pic_name)
    {
        $gapDir = substr($pic_name, 0, 2);
        if ($soure = self::SOURCE_TAIWAN) {
            return "/img/tw/$gapDir/$pic_name";
        } elseif($soure = self::SOURCE_HANYU) {
            return "/img/hy/$pic_name.png";
        } elseif($soure = self::SOURCE_GAOLI) {
            return "/img/gl/$gapDir/$pic_name.png";
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'type', 'nor_var_type', 'frequence', 'bduplicate', 'stocks', 'bhard', 'created_at', 'updated_at'], 'integer'],
            [['word', 'radical', 'structure'], 'string', 'max' => 8],
            [['pic_name', 'belong_standard_word_code', 'standard_word_code', 'pinyin'], 'string', 'max' => 64],
            [['position_code', 'duplicate_id', 'zhengma', 'wubi',  'similar_stock'], 'string', 'max' => 128],
            [['min_split', 'deform_split', 'stock_serial', 'remark'], 'string', 'max' => 256],
            [['max_split', 'mix_split'], 'string', 'max' => 512],
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
            'type' => Yii::t('app', '类型'),
            'word' => Yii::t('app', '文字'),
            'pic_name' => Yii::t('app', '图片'),
            'frequence' => Yii::t('app', '频率'),
            'nor_var_type' => Yii::t('app', '正异关系'),
            'belong_standard_word_code' => Yii::t('app', '所属正字'),
            'standard_word_code' => Yii::t('app', '兼正字号'),
            'position_code' => Yii::t('app', '位置编码'),
            'bduplicate' => Yii::t('app', '是否重复'),
            'duplicate_id' => Yii::t('app', '重复ID'),
            'pinyin' => Yii::t('app', '拼音'),
            'radical' => Yii::t('app', '部首'),
            'stocks' => Yii::t('app', '笔画'),
            'zhengma' => Yii::t('app', '郑码'),
            'wubi' => Yii::t('app', '五笔'),
            'structure' => Yii::t('app', '结构'),
            'bhard' => Yii::t('app', '是否难字'),
            'min_split' => Yii::t('app', '最小拆分'),
            'deform_split' => Yii::t('app', '调笔拆分'),
            'similar_stock' => Yii::t('app', '相似部件'),
            'max_split' => Yii::t('app', '最大拆分'),
            'mix_split' => Yii::t('app', '混合拆分'),
            'stock_serial' => Yii::t('app', '部件序列'),
            'remark' => Yii::t('app', '备注'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
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
