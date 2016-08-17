<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%lq_variant}}".
 *
 * @property string $id
 * @property integer $source
 * @property string $pic_name
 * @property string $variant_code
 * @property string $belong_standard_word_code
 * @property integer $nor_var_type
 */
class LqVariant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lq_variant}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'nor_var_type'], 'integer'],
            [['pic_name', 'variant_code', 'belong_standard_word_code'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'source' => Yii::t('common', 'Source'),
            'pic_name' => Yii::t('common', 'Pic Name'),
            'variant_code' => Yii::t('common', 'Variant Code'),
            'belong_standard_word_code' => Yii::t('common', 'Belong Standard Word Code'),
            'nor_var_type' => Yii::t('common', 'Nor Var Type'),
        ];
    }
}
