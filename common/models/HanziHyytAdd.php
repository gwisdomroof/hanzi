<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%hanzi_hyyt_add}}".
 *
 * @property string $id
 * @property integer $page
 * @property integer $num
 * @property integer $type2
 * @property string $tong_word2
 * @property string $zhushi2
 */
class HanziHyytAdd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_hyyt_add}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page', 'num', 'type2'], 'integer'],
            [['tong_word2'], 'string', 'max' => 32],
            [['zhushi2'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'page' => Yii::t('common', 'Page'),
            'num' => Yii::t('common', 'Num'),
            'type2' => Yii::t('common', 'Type2'),
            'tong_word2' => Yii::t('common', 'Tong Word2'),
            'zhushi2' => Yii::t('common', 'Zhushi2'),
        ];
    }
}
