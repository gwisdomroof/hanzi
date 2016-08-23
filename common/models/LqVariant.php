<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%lq_variant}}".
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
 * @property integer $duplicate
 * @property string $duplicate_id
 * @property integer $frequence
 * @property string $sutra_ids
 * @property integer $bconfirm
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
class LqVariant extends HanziSet
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
        return array_merge(parent::rules(), [
            [['bconfirm'], 'integer'],
            [['sutra_ids'], 'string', 'max' => 256]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sutra_ids' => Yii::t('common', '经字号'),
            'bconfirm' => Yii::t('common', '是否存疑'),
        ]);
    }
}
