<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\MemberRelation;

/**
 * This is the model class for table "{{%hanzi_task}}".
 *
 * @property string $id
 * @property integer $leader_id
 * @property integer $user_id
 * @property integer $page
 * @property integer $seq
 * @property integer $startid
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class HanziTask extends \yii\db\ActiveRecord
{
    const STATUS_ASSIGNMENT = 0;
    const STATUS_ONGOING = 1;
    const STATUS_CANCEL = 2;
    const STATUS_COMPLETE = 3;

    const SEQ_FIRST = 1;
    const SEQ_SECOND = 2;
    const SEQ_THIRD = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_task}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'leader_id', 'page', 'seq', 'start_id', 'end_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['leader.username', 'member.username', 'created_at', 'updated_at'], 'safe'],
            [['remark'], 'string', 'max' => 128],
            ['page', function ($attribute, $params) {
                if ($this->status == self::STATUS_CANCEL) {
                    return true;
                }
                // 1. create, current page is not allowed duplicated except the record is canceled
                $query = HanziTask::find()->where(['page' => $this->page])->andwhere(['!=', 'status', self::STATUS_CANCEL]);
                // update
                if (!empty($this->id)) {
                    $query->andFilterWhere(['!=', 'id', $this->id]);
                }
                if ($query->exists()) {
                    $this->addError('page', '该页面已分配。');
                }
            }],
        ];
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getMember()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getLeader()
    {
        return $this->hasOne(User::className(), ['id' => 'leader_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'leader_id' => Yii::t('app', '组长'),
            'user_id' => Yii::t('app', '组员'),
            'leader.username' => Yii::t('app', '组长'),
            'member.username' => Yii::t('app', '组员'),
            'page' => Yii::t('app', '页码'),
            'seq' => Yii::t('app', '阶段'),
            'start_id' => Yii::t('app', '起始ID'),
            'end_id' => Yii::t('app', '结束ID'),
            'status' => Yii::t('app', '状态'),
            'remark' => Yii::t('app', '备注'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
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


    public function attributes()
    {
        // 添加关联字段到可搜索特性
        return array_merge(parent::attributes(), ['member.username', 'leader.username']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $idRange = Hanzi::getIdRangeByPage($this->page);
            $this->start_id = $idRange['minId']; 
            $this->end_id = $idRange['maxId'];
            $this->leader_id = Yii::$app->user->id;
            return true;

        } else {
            return false;
        }
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function getSeq($userId, $id)
    {       
        $model = HanziTask::find()->where(['user_id'=>$userId])->andwhere(['<=', 'start_id', $id])->andwhere(['>=', 'end_id', $id])->one();
        return empty($model) ? null : $model->seq;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function checkIdPermission($userId, $id, $seq)
    {       
        return HanziTask::find()->where(['user_id'=>$userId, 'seq'=>$seq])->andwhere(['<=', 'start_id', $id])->andwhere(['>=', 'end_id', $id])->exists();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function checkPagePermission($userId, $page, $seq)
    {
        return HanziTask::find()->where(['user_id'=>$userId, 'page'=>$page, 'seq'=>$seq])->exists();
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function seqs()
    {
        return [
            self::SEQ_FIRST => Yii::t('common', '初次拆分'),
            self::SEQ_SECOND => Yii::t('common', '二次拆分'),
            self::SEQ_THIRD => Yii::t('common', '审查'),
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_ASSIGNMENT => Yii::t('common', '初分配'),
            self::STATUS_ONGOING => Yii::t('common', '进行中'),
            self::STATUS_CANCEL => Yii::t('common', '已取消'),            
            self::STATUS_COMPLETE => Yii::t('common', '已完成')
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function isLeader($id)
    {
        $roles = Yii::$app->authManager->getRolesByUser($id);
        return array_key_exists('组长', $roles);
    }

    

    /**
     * @param get members of leader
     * @return mixed
     */
    public function members()
    {
        $items[Yii::$app->user->id] = Yii::$app->user->identity->username;
        $users = MemberRelation::find()->with('member')->where(['leader_id'=> Yii::$app->user->id])->all();
        foreach ($users as $user) {
          $items[$user->member_id] = $user->member->username;
        }
        return $items;
    }

}
