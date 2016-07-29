<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "hanzi_gaoli_dedup".
 *
 * @property string $id
 * @property string $zhengma
 * @property integer $zmcnt
 * @property integer $page
 */
class HanziGaoliDedup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hanzi_gaoli_dedup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zhengma', 'zmcnt'], 'required'],
            [['zmcnt', 'page'], 'integer'],
            [['zhengma'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'zhengma' => Yii::t('common', 'Zhengma'),
            'zmcnt' => Yii::t('common', 'Zmcnt'),
            'page' => Yii::t('common', 'Page'),
        ];
    }
}
