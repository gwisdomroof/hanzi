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
            [['source', 'type', 'nor_var_type', 'duplicate', 'frequence', 'bconfirm', 'stocks', 'bhard', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['word', 'radical', 'structure'], 'string', 'max' => 8],
            [['pic_name', 'belong_standard_word_code', 'standard_word_code', 'pinyin'], 'string', 'max' => 64],
            [['position_code', 'duplicate_id', 'zhengma', 'wubi', 'min_split', 'deform_split', 'similar_stock'], 'string', 'max' => 128],
            [['sutra_ids', 'max_split', 'mix_split', 'stock_serial', 'remark'], 'string', 'max' => 256],
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
            'type' => Yii::t('common', 'Type'),
            'word' => Yii::t('common', 'Word'),
            'pic_name' => Yii::t('common', 'Pic Name'),
            'nor_var_type' => Yii::t('common', 'Nor Var Type'),
            'belong_standard_word_code' => Yii::t('common', 'Belong Standard Word Code'),
            'standard_word_code' => Yii::t('common', 'Standard Word Code'),
            'position_code' => Yii::t('common', 'Position Code'),
            'duplicate' => Yii::t('common', 'Duplicate'),
            'duplicate_id' => Yii::t('common', 'Duplicate ID'),
            'frequence' => Yii::t('common', 'Frequence'),
            'sutra_ids' => Yii::t('common', 'Sutra Ids'),
            'bconfirm' => Yii::t('common', 'Bconfirm'),
            'pinyin' => Yii::t('common', 'Pinyin'),
            'radical' => Yii::t('common', 'Radical'),
            'stocks' => Yii::t('common', 'Stocks'),
            'zhengma' => Yii::t('common', 'Zhengma'),
            'wubi' => Yii::t('common', 'Wubi'),
            'structure' => Yii::t('common', 'Structure'),
            'bhard' => Yii::t('common', 'Bhard'),
            'min_split' => Yii::t('common', 'Min Split'),
            'deform_split' => Yii::t('common', 'Deform Split'),
            'similar_stock' => Yii::t('common', 'Similar Stock'),
            'max_split' => Yii::t('common', 'Max Split'),
            'mix_split' => Yii::t('common', 'Mix Split'),
            'stock_serial' => Yii::t('common', 'Stock Serial'),
            'remark' => Yii::t('common', 'Remark'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }
}
