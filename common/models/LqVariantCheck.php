<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%lq_variant_check}}".
 *
 * @property string $id
 * @property integer $source
 * @property string $pic_name
 * @property string $variant_code1
 * @property string $belong_standard_word_code1
 * @property integer $nor_var_type1
 * @property string $variant_code2
 * @property string $belong_standard_word_code2
 * @property integer $nor_var_type2
 * @property integer $bconfirm
 * @property string $remark
 */
class LqVariantCheck extends \yii\db\ActiveRecord
{
    // 难易等级
    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;


    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function levels()
    {
        return [
            self::LEVEL_ONE => Yii::t('frontend', '一'),
            self::LEVEL_TWO => Yii::t('frontend', '二'),
            self::LEVEL_THREE => Yii::t('frontend', '三')
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lq_variant_check}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'nor_var_type1', 'nor_var_type2', 'level1', 'level2', 'bconfirm', 'created_at', 'updated_at'], 'integer'],
            [['pic_name', 'variant_code1', 'belong_standard_word_code1', 'variant_code2', 'belong_standard_word_code2'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'source' => Yii::t('common', '来源'),
            'pic_name' => Yii::t('common', '图片名'),
            'variant_code1' => Yii::t('common', '异体字编号'),
            'belong_standard_word_code1' => Yii::t('common', '所属正字'),
            'nor_var_type1' => Yii::t('common', '正异类型'),
            'level1' => Yii::t('common', '等级'),
            'variant_code2' => Yii::t('common', '异体字编号'),
            'belong_standard_word_code2' => Yii::t('common', '所属正字'),
            'nor_var_type2' => Yii::t('common', '正异类型'),
            'level2' => Yii::t('common', '等级'),
            'bconfirm' => Yii::t('common', '是否确定'),
            'remark' => Yii::t('common', '备注'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function isNew()
    {
        if (!empty($this->variant_code2) || !empty($this->nor_var_type2)) {
            return false;
        } else {
           return true; 
        }
    }

}
