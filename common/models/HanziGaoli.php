<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%hanzi_gaoli}}".
 *
 * @property string $id
 * @property string $glyph
 * @property string $code
 * @property integer $busu_id
 * @property integer $totalstroke
 * @property integer $reststroke
 * @property string $jungma
 * @property string $standard
 * @property string $ksound
 * @property string $kmean
 * @property string $banjul
 * @property string $csound
 * @property string $cmean
 * @property string $jsound
 * @property string $jmean
 * @property string $emean
 */
class HanziGaoli extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_gaoli}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['busu_id', 'totalstroke', 'reststroke'], 'integer'],
            [['glyph', 'code', 'standard', 'ksound'], 'string', 'max' => 16],
            [['jungma', 'banjul', 'csound', 'jsound'], 'string', 'max' => 64],
            [['kmean', 'cmean', 'jmean', 'emean'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'glyph' => Yii::t('common', 'Glyph'),
            'code' => Yii::t('common', 'Code'),
            'busu_id' => Yii::t('common', 'Busu ID'),
            'totalstroke' => Yii::t('common', 'Totalstroke'),
            'reststroke' => Yii::t('common', 'Reststroke'),
            'jungma' => Yii::t('common', 'Jungma'),
            'standard' => Yii::t('common', 'Standard'),
            'ksound' => Yii::t('common', 'Ksound'),
            'kmean' => Yii::t('common', 'Kmean'),
            'banjul' => Yii::t('common', 'Banjul'),
            'csound' => Yii::t('common', 'Csound'),
            'cmean' => Yii::t('common', 'Cmean'),
            'jsound' => Yii::t('common', 'Jsound'),
            'jmean' => Yii::t('common', 'Jmean'),
            'emean' => Yii::t('common', 'Emean'),
        ];
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getHanziBusu()
    {
        return $this->hasOne(HanziBusu::className(), ['busu_id' => 'busu_id']);
    }

}
