<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%hanzi_busu}}".
 *
 * @property integer $busu_id
 * @property string $glyph
 * @property integer $busu_stroke
 */
class HanziBusu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_busu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['busu_id'], 'required'],
            [['busu_id', 'busu_stroke'], 'integer'],
            [['glyph'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'busu_id' => Yii::t('common', 'Busu ID'),
            'glyph' => Yii::t('common', 'Glyph'),
            'busu_stroke' => Yii::t('common', 'Busu Stroke'),
        ];
    }
}
