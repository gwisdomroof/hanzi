<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%hanzi_image}}".
 *
 * @property string $id
 * @property integer $source
 * @property string $name
 * @property string $value
 */
class HanziImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source'], 'integer'],
            [['value'], 'required'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'source' => Yii::t('app', 'Source'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'Value'),
        ];
    }


    public static function getImage($fn)
    {
        $model = HanziImage::find()->where(['name' => $fn])->one();
        return empty($model) ? '' : $model->value;
    }
}
