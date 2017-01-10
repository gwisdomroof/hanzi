<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "hanzi_parts_update".
 *
 * @property string $id
 * @property string $find_str
 * @property string $replace_str
 * @property string $id_set
 * @property string $remark
 */
class HanziPartsUpdate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hanzi_parts_update';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['find_str', 'replace_str'], 'string', 'max' => 64],
            [['id_set'], 'string', 'max' => 8],
            [['remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'find_str' => Yii::t('frontend', 'Find Str'),
            'replace_str' => Yii::t('frontend', 'Replace Str'),
            'id_set' => Yii::t('frontend', 'Id Set'),
            'remark' => Yii::t('frontend', 'Remark'),
        ];
    }
}
