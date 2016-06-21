<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\User;

/**
 * This is the model class for table "{{%member_relation}}".
 *
 * @property string $id
 * @property integer $member_id
 * @property integer $leader_id
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $relation_type
 */
class MemberRelation extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 0;

    const TYPE_SPLIT = 1;
    const TYPE_INPUT = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'leader_id'], 'required'],
            [['created_at', 'updated_at', 'relation_type'], 'safe'],
            [['member_id', 'leader_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['remark'], 'string', 'max' => 128],
        ];
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'member_id' => Yii::t('app', '组员'),
            'leader_id' => Yii::t('app', '组长'),
            'relation_type' => Yii::t('app', '类型'),
            'status' => Yii::t('app', '状态'),
            'remark' => Yii::t('app', '备注'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '修改时间'),
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('common', '启用'),
            self::STATUS_BLOCKED => Yii::t('common', '禁用'),
        ];
    }

    /**
     * Returns user types list
     * @return array|mixed
     */
    public static function types()
    {
        return [
            self::TYPE_SPLIT => Yii::t('common', '异体字拆字'),
            self::TYPE_INPUT => Yii::t('common', '异体字录入'),
        ];
    }

    /**
     * get members from users
     * @param string $id
     * @return mixed
     */
    public static function leaders()
    {
        $items = [];
        $users = User::getUsersByRole("组长");
        foreach ($users as $user) {
          $items[$user['user_id']] = $user['username'];
        }
        return $items;
    }


    /**
     * get members from users
     * @param 
     * @return mixed
     */
    public static function members()
    {
        $items = [];
        $users = User::getUsersByRole("拆字员");
        foreach ($users as $user) {
          $items[$user['user_id']] = $user['username'];
        }
        return $items;
    }

    /**
     * get members of leaders
     * @param 
     * @return mixed
     */
    public function getMember()
    {
        return $this->hasOne(User::className(), ['id' => 'member_id']);
    }

}
