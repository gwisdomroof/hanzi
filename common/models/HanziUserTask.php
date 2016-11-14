<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%hanzi_user_task}}".
 *
 * @property string $id
 * @property string $userid
 * @property string $taskid: 这个taskid指的是拆字或识别的id
 * @property integer $task_type
 * @property integer $task_seq
 * @property integer $task_status
 * @property integer $quality
 * @property integer $created_at
 * @property integer $updated_at
 */
class HanziUserTask extends \yii\db\ActiveRecord
{
    const TYPE_ALL = 0;
    const TYPE_SPLIT = 1;
    const TYPE_INPUT = 2;
    const TYPE_COLLATE = 3;
    const TYPE_DOWNLOAD = 4;
    const TYPE_DEDUP = 5;
    const TYPE_GAOLI_SPLIT = 6;

    const DEDUP_WEIGHT = 2;
    const SPLIT_WEIGHT = 2;
    const INPUT_WEIGHT = 1;

    public $cnt;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_user_task}}';
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
    public function rules()
    {
        return [
            [['userid', 'quality'], 'required'],
            [['userid', 'taskid', 'task_type', 'task_seq', 'task_status', 'quality', 'created_at', 'updated_at', 'cnt'], 'integer'],
            [['remark', 'user.username'], 'safe'],
        ];
    }

    /**
     * 获取从某日开始已完成工作量
     * @param int $userId 用户名
     * @param int $taskType 任务类型
     * @param int $start 开始日期的时间，timestamp的整数
     * @return mixed
     */
    public static function getFinishedWorkCountFrom($userId, $taskType = self::TYPE_SPLIT, $start)
    {
        $begin = mktime(0, 0, 0, date('m', $start), date('d', $start), date('Y', $start));
        $end = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        return HanziUserTask::find()
            ->where(['userid' => $userId, 'task_type' => $taskType])
            ->andWhere(['>=', 'created_at', $begin])
            ->andWhere(['<=', 'created_at', $end])
            ->count();
    }

    /**
     * 获取今日已完成工作量
     * @param string $id
     * @return mixed
     */
    public static function getFinishedWorkCountToday($userId, $taskType = self::TYPE_SPLIT)
    {
        $begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        return HanziUserTask::find()
            ->where(['userid' => $userId, 'task_type' => $taskType])
            ->andWhere(['>=', 'created_at', $begin])
            ->andWhere(['<=', 'created_at', $end])
            ->count();
    }

    /**
     * 获取用户对应的积分.
     * @return
     */
    public static function getScore($userid)
    {
        $count = HanziUserTask::find()->select('sum(quality) as count')->where(['userid' => $userid])->asArray()->one();
        return (int)$count['count'];
    }

    /**
     * @inheritdoc
     *
     */
    public static function addItem($userid, $taskid, $taskType, $quality, $taskSeq)
    {
        $userTask = new HanziUserTask();
        $userTask->userid = $userid;
        $userTask->taskid = $taskid;
        $userTask->task_type = $taskType;
        $userTask->task_seq = $taskSeq;
        $userTask->quality = $quality;

        $ifExist = HanziUserTask::find()->where([
//            'userid' => $userTask->userid,
            'taskid' => $userTask->taskid,
            'task_type' => $userTask->task_type,
            'task_seq' => $userTask->task_seq])
            ->exists();

        if (!$ifExist && $userTask->save()) {
            // 数据不存在且保存成功
            return true;
        } else {
            return false;
        }
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }

    public function attributes()
    {
        // 添加关联字段到可搜索特性
        return array_merge(parent::attributes(), ['user.username']);
    }

    /**
     * 任务类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function types($outOfSystem = false)
    {
        if ($outOfSystem) {
            return [
                self::TYPE_INPUT => Yii::t('common', '异体字录入'),
                self::TYPE_COLLATE => Yii::t('common', '图书校对'),
                self::TYPE_DOWNLOAD => Yii::t('common', '论文下载'),
            ];
        }
        return [
            self::TYPE_ALL => Yii::t('common', '总积分'),
            self::TYPE_SPLIT => Yii::t('common', '异体字拆字'),
            self::TYPE_INPUT => Yii::t('common', '异体字录入'),
            self::TYPE_COLLATE => Yii::t('common', '图书校对'),
            self::TYPE_DOWNLOAD => Yii::t('common', '论文下载'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'userid' => Yii::t('common', '用户ID'),
            'taskid' => Yii::t('common', '任务ID'),
            'task_type' => Yii::t('common', '任务类型'),
            'task_seq' => Yii::t('common', '任务阶段'),
            'task_status' => Yii::t('common', '任务状态'),
            'quality' => Yii::t('common', '分值'),
            'remark' => Yii::t('common', '备注'),
            'created_at' => Yii::t('common', '创建时间'),
            'updated_at' => Yii::t('common', '更新时间'),
            'cnt' => Yii::t('common', '积分'),
            'user.username' => Yii::t('common', '用户名'),
        ];
    }
}
