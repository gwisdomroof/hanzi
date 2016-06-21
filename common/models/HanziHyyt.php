<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%hanzi_hy_yt}}".
 *
 * @property string $id
 * @property string $volume
 * @property integer $page
 * @property integer $num
 * @property string $picture
 * @property string $word1
 * @property integer $type1
 * @property string $tong_word1
 * @property string $zhushi1
 * @property string $word2
 * @property integer $type2
 * @property string $tong_word2
 * @property string $zhushi2
 * @property string $word3
 * @property integer $type3
 * @property string $tong_word3
 * @property string $zhushi3
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class HanziHyyt extends \yii\db\ActiveRecord
{
    const TYPE_XIAYI = 1;
    const TYPE_JIANHUA = 2;
    const TYPE_LEITUIJIANHUA = 3;
    const TYPE_EZI = 4;
    const TYPE_GUJIN = 5;
    const TYPE_AT = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_hy_yt}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page', 'num', 'type1', 'type2', 'type3', 'created_at', 'updated_at'], 'integer'],
            [['volume', 'word1', 'word2', 'word3'], 'string', 'max' => 8],
            [['picture', 'tong_word1', 'tong_word2', 'tong_word3'], 'string', 'max' => 32],
            [['zhushi1', 'zhushi2', 'zhushi3'], 'string', 'max' => 64],
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
            'volume' => Yii::t('common', '册'),
            'page' => Yii::t('common', '页'),
            'num' => Yii::t('common', '序号'),
            'picture' => Yii::t('common', '图片'),
            'word1' => Yii::t('common', '文字'),
            'type1' => Yii::t('common', '类型'),
            'tong_word1' => Yii::t('common', '同某字'),
            'zhushi1' => Yii::t('common', '备注'),
            'word2' => Yii::t('common', '文字'),
            'type2' => Yii::t('common', '类型'),
            'tong_word2' => Yii::t('common', '同某字'),
            'zhushi2' => Yii::t('common', '备注'),
            'word3' => Yii::t('common', '文字'),
            'type3' => Yii::t('common', '类型'),
            'tong_word3' => Yii::t('common', '同某字'),
            'zhushi3' => Yii::t('common', '备注'),
            'remark' => Yii::t('common', '备注'),
            'created_at' => Yii::t('common', '创建时间'),
            'updated_at' => Yii::t('common', '修改时间'),
        ];
    }

        /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function types()
    {
        return [
            self::TYPE_XIAYI => Yii::t('common', '狭义异体字'),
            self::TYPE_JIANHUA => Yii::t('common', '简化字'),
            self::TYPE_LEITUIJIANHUA => Yii::t('common', '类推简化字'),
            self::TYPE_EZI => Yii::t('common', '讹字'),
            self::TYPE_GUJIN => Yii::t('common', '古今字'),
            self::TYPE_AT => Yii::t('common', '@')
        ];
    }

}
