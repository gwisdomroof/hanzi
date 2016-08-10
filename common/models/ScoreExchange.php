<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%score_exchange}}".
 *
 * @property string $id
 * @property integer $userid
 * @property integer $type
 * @property integer $score
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class ScoreExchange extends \yii\db\ActiveRecord
{
    # 奖品类型
    const TYPE_GWRS = 1;
    const TYPE_LQS365_2011 = 2;
    const TYPE_LQS365_2012 = 3;
    const TYPE_LQS365_2013 = 4;
    const TYPE_LQS365_2014 = 5;

    # 状态
    const STATUS_ASSIGNMENT = 1;
    const STATUS_COMPLETE = 2;

    /**
     * 状态类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_ASSIGNMENT => Yii::t('common', '兑换中'),
            self::STATUS_COMPLETE => Yii::t('common', '已兑换')
        ];
    }

    /**
     * 状态类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function types()
    {
        return [
            self::TYPE_GWRS => Yii::t('common', '《感悟人生》（英文版）'),
            self::TYPE_LQS365_2011 => Yii::t('common', '《龙泉寺的365天》（2011年）'),
            self::TYPE_LQS365_2012 => Yii::t('common', '《龙泉寺的365天》（2012年）'),            
            self::TYPE_LQS365_2013 => Yii::t('common', '《龙泉寺的365天》（2013年）'),
            self::TYPE_LQS365_2014 => Yii::t('common', '《龙泉寺的365天》（2014年）')
        ];
    }

    /**
     * 积分
     * Returns user score list
     * @return array|mixed
     */
    public static function scores()
    {
        return [
            self::TYPE_GWRS => Yii::t('common', '1000'),
            self::TYPE_LQS365_2011 => Yii::t('common', '2000'),
            self::TYPE_LQS365_2012 => Yii::t('common', '2000'),            
            self::TYPE_LQS365_2013 => Yii::t('common', '3000'),
            self::TYPE_LQS365_2014 => Yii::t('common', '3000')
        ];
    }

    /**
     * 状态类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function typesWithScore()
    {
        return [
            self::TYPE_GWRS => Yii::t('common', '《感悟人生》（英文版）/1000分'),
            self::TYPE_LQS365_2011 => Yii::t('common', '《龙泉寺的365天》（2011年）/2000分'),
            self::TYPE_LQS365_2012 => Yii::t('common', '《龙泉寺的365天》（2012年）/2000分'),            
            self::TYPE_LQS365_2013 => Yii::t('common', '《龙泉寺的365天》（2013年）/3000分'),
            self::TYPE_LQS365_2014 => Yii::t('common', '《龙泉寺的365天》（2014年）/3000分')
        ];
    }

    /**
     * 获取用户对应的积分.
     * @return
     */
    public static function getScore($userid)
    {
        $count = ScoreExchange::find()->select('sum(score) as count')->where(['userid' => $userid])->asArray()->one();
        return (int)$count['count'];
    }


    public function attributes()
    {
        // 添加关联字段到可搜索特性
        return array_merge(parent::attributes(), ['user.username']);
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%score_exchange}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid'], 'required'],
            [['userid', 'type', 'score', 'status', 'created_at', 'updated_at'], 'integer'],
            [['user.username'], 'safe'],
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
            'userid' => Yii::t('common', '用户'),
            'type' => Yii::t('common', '奖品'),
            'score' => Yii::t('common', '所用分值'),
            'status' => Yii::t('common', '状态'),
            'remark' => Yii::t('common', '备注'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'user.username' => Yii::t('common', '用户名')
        ];
    }
}
