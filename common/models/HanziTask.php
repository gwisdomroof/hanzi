<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\MemberRelation;
use common\models\HanziHyyt;

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
 * @property integer $task_type
 */
class HanziTask extends \yii\db\ActiveRecord
{
    const TYPE_SPLIT = 1;
    const TYPE_INPUT = 2;
    const TYPE_COLLATE = 3;
    const TYPE_DOWNLOAD = 4;

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
            [['user_id', 'leader_id', 'page', 'seq', 'start_id', 'end_id', 'status', 'created_at', 'updated_at', 'task_type'], 'integer'],
            [['page'], 'integer', 'max' => 10000],
            [['leader.username', 'member.username', 'created_at', 'updated_at'], 'safe'],
            [['remark'], 'string', 'max' => 128],
            ['page', function ($attribute, $params) {
                if ($this->status == self::STATUS_CANCEL) {
                    return true;
                }
                // 1. create, current page is not allowed duplicated except the record is canceled
                $query = HanziTask::find()->where(['page' => $this->page, 'task_type' => $this->task_type])->andwhere(['!=', 'status', self::STATUS_CANCEL]);
                // 2. update, except current id
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
     * 获取系统组长
     * @return [type] [description]
     */
    public static function getSystemLeader()
    {
        return User::getUsersByRole("系统组长")[0];
    }

    /**
     * 根据任务类型，获取当前可分配的页码
     * @return [type] [description]
     */
    public static function getIdlePages($type = self::TYPE_SPLIT, $count = 50)
    {
        // 待处理的记录总数
        $maxPageNumber = 0;
        if ($type == self::TYPE_SPLIT) {
          $countToSplit = HanziSplit::find()->where(['duplicate' => 0])->count();
          $maxPageNumber = (int)($countToSplit / Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false))+ 1;
        } else {
          $maxPageNumber = 5127; // 汉语大字典最大5127页
        }

        // 已申请的任务页码
        $usedPages = HanziTask::find()->select('page')->orderBy('id')->where(['task_type'=>$type])->AndWhere(['!=', 'status', self::STATUS_CANCEL])->asArray()->all();
        $usedPagesArr = [];
        foreach ($usedPages as $page) {
          if (!empty($page['page']))
            $usedPagesArr[] = $page['page']; 
        }

        $idlePagesArr = [];
        for ($i=1; $i < $maxPageNumber; $i++) { 
          if (!in_array($i, $usedPagesArr)) {
            $idlePagesArr[$i] = $i;
            if (count($idlePagesArr) >= $count) {
              break;
            }
          }
        }

        return $idlePagesArr;

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
            'task_type' => Yii::t('app', '任务类型'),
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
            // 如果是汉字拆分任务，则须保存id范围
            if ($this->task_type == self::TYPE_SPLIT) {
              $idRange = HanziSplit::getIdRangeByPage($this->page);
              $this->start_id = $idRange['minId']; 
              $this->end_id = $idRange['maxId'];
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据任务ID获取任务对应的阶段
     * @param string $id
     * @return mixed
     */
    public static function getSeq($userId, $id, $taskType=1)
    {       
        $model = HanziTask::find()->where(['user_id'=>$userId, 'task_type'=>$taskType])->andwhere(['<=', 'start_id', $id])->andwhere(['>=', 'end_id', $id])->OrderBy('seq')->one();
        return empty($model) ? null : $model->seq;
    }

    /**
     * 根据任务ID获取任务对应的阶段
     * @param string $id
     * @return mixed
     */
    public static function getSeqByPage($userId, $page, $taskType=1)
    {       
        $model = HanziTask::find()->where(['user_id'=>$userId, 'task_type'=>$taskType, 'page'=>$page])->OrderBy(['created_at'=>SORT_DESC])->one();
        return empty($model) ? null : $model->seq;
    }

    /**
     * 如果有未完成的任务，则不能继续申请
     * @param string $id
     * @return mixed
     */
    public static function checkApplyPermission($userId, $taskType)
    {       
        return HanziTask::find()->where(['user_id'=>$userId, 'task_type'=>$taskType])->andwhere(['<=', 'status', self::STATUS_ONGOING])->exists();
    }

    /**
     * 拆字任务，检查是否有对该字的拆分权限
     * @param string $id
     * @return mixed
     */
    public static function checkIdPermission($userId, $id, $seq, $taskType=self::TYPE_SPLIT)
    {       
        return HanziTask::find()->where(['user_id'=>$userId, 'seq'=>$seq, 'task_type'=>$taskType])->andwhere(['<=', 'start_id', $id])->andwhere(['>=', 'end_id', $id])->exists();
    }

    /**
     * 拆字任务，检查是否有对该页的拆分权限
     * @param string $id
     * @return mixed
     */
    public static function checkPagePermission($userId, $page, $seq=0, $taskType=self::TYPE_SPLIT)
    {
        $query = HanziTask::find()->where(['user_id'=>$userId, 'page'=>$page, 'task_type'=>$taskType]);
        if ($seq !== 0) {
          $query->andwhere(['seq'=>$seq]);
        }
        return $query->exists();
    }

    /**
     * 阶段
     * Returns user statuses list
     * @return array|mixed
     */
    public static function seqs()
    {
        return [
            self::SEQ_FIRST => Yii::t('common', '初次'),
            self::SEQ_SECOND => Yii::t('common', '二次'),
            self::SEQ_THIRD => Yii::t('common', '审查'),
        ];
    }

    /**
     * 状态类型
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
     * 任务类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function types()
    {
        return [
            self::TYPE_SPLIT => Yii::t('common', '异体字拆字'),
            self::TYPE_INPUT => Yii::t('common', '异体字录入'),
            self::TYPE_COLLATE => Yii::t('common', '图书校对'),
            self::TYPE_DOWNLOAD => Yii::t('common', '论文下载'),
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
