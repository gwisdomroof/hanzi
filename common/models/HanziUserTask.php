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
    const TYPE_SPLIT = 1;
    const TYPE_INPUT = 2;

    const SPLIT_WEIGHT = 1;
    const INPUT_WEIGHT = 1;

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
            [['task_type', 'task_seq', 'task_status', 'quality', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * 获取用户对应的积分.
     * @return
     */
    public static function getScore($userid)
    {
        $splitIds = HanziUserTask::find()->where(['userid' => $userid, 'task_type' => self::TYPE_SPLIT])->count();

        $inputIds = HanziUserTask::find()->where(['userid' => $userid, 'task_type' => self::TYPE_INPUT])->count();

        return $splitIds * self::SPLIT_WEIGHT + $inputIds * self::INPUT_WEIGHT;
    }

    /**
     * @inheritdoc
     */
    public static function addItem($userid, $taskid, $taskType, $taskSeq, $bUpdateSession = true)
    {
        $userTask = new HanziUserTask();
        $userTask->userid = $userid;
        $userTask->taskid = $taskid;
        $userTask->task_type = $taskType;
        $userTask->task_seq = $taskSeq;
        if (!HanziUserTask::find()->where(['userid'=>$userTask->userid, 'taskid'=>$userTask->taskid, 'task_type'=>$userTask->task_type, 'task_seq'=>$userTask->task_seq])->exists() && !$userTask->save()) {
            var_dump($userTask->getErrors());
            die;
        }

        if ($bUpdateSession) {
            Yii::$app->session->set('cur_scores', self::getScore($userid));
        }

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
            'quality' => Yii::t('common', '质量'),
            'created_at' => Yii::t('common', '创建时间'),
            'updated_at' => Yii::t('common', '更新时间'),
        ];
    }
}
