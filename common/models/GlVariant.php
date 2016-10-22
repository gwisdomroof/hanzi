<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%gltw_dedup_result}}".
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
 * @property string $duplicate_id1
 * @property string $duplicate_id2
 * @property string $duplicate_id3
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class GlVariant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gl_variant}}';
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
    public function rules()
    {
        return [
            [['source', 'type', 'nor_var_type', 'created_at', 'updated_at'], 'integer'],
            [['word'], 'string', 'max' => 8],
            [['pic_name', 'belong_standard_word_code', 'standard_word_code'], 'string', 'max' => 64],
            [['position_code', 'duplicate_id1', 'duplicate_id2', 'duplicate_id3', 'remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'source' => Yii::t('frontend', '来源'),
            'type' => Yii::t('frontend', '类型'),
            'word' => Yii::t('frontend', '文字'),
            'pic_name' => Yii::t('frontend', '图片'),
            'nor_var_type' => Yii::t('frontend', '正异关系'),
            'belong_standard_word_code' => Yii::t('frontend', '所属正字'),
            'standard_word_code' => Yii::t('frontend', '兼正字号'),
            'position_code' => Yii::t('frontend', '位置编码'),
            'duplicate_id1' => Yii::t('frontend', '初次：重复值'),
            'duplicate_id2' => Yii::t('frontend', '回查：重复值'),
            'duplicate_id3' => Yii::t('frontend', '审查：重复值'),
            'remark' => Yii::t('frontend', '备注'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

}
