<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%work_clock}}".
 *
 * @property string $id
 * @property integer $userid
 * @property integer $type
 * @property integer $amount
 * @property string $content
 * @property integer $created_at
 * @property integer $updated_at
 */
class WorkClock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%work_clock}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['userid', 'type', 'amount', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string', 'max' => 1024],
            [['type'], function ($attribute, $params) {
                // 一种类型，当日只能有一次打卡记录
                if ($this->isNewRecord) {
                    $begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    $end = time();
                    $model = WorkClock::find()->where(['userid' => Yii::$app->user->id])
                        ->andWhere(['type' => $this->type])
                        ->andWhere(['>=', 'created_at', $begin])
                        ->andWhere(['<=', 'created_at', $end])
                        ->one();
                    if (!empty($model)) {
                        $this->addError('type', '一种类型的任务，每天只能打一次卡。');
                    }
                }
            }],
            [['content'], function ($attribute, $params) {
                // 获取本日计划工作量
                $curWorkPackage = WorkPackage::getCurWorkPackage(Yii::$app->user->id, $this->type);
                if (empty($curWorkPackage)) {
                    $this->addError('content', '请您先领取任务包，完成每日工作再打卡。');
                    return;
                }
                $schedule = (int)$curWorkPackage->daily_schedule;
                // 获取本日实际工作量
                $actual = (int)$curWorkPackage->getFinishedToday();
                if ($actual < $schedule)
                    $this->addError('content', '请您先完成本日的计划工作再打卡。');
            }]
        ];
    }

    /**
     * @param $type
     */
    public static function ifClockedToday($userid, $type)
    {
        return WorkClock::find()->where(['userid' => $userid, 'type' => $type])
            ->andWhere(['>=', 'created_at', mktime(0, 0, 0, date('m'), date('d'), date('Y'))])
            ->andWhere(['<=', 'created_at', time()])
            ->exists();
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        // 设置默认值
        $userid = Yii::$app->user->id;
        $this->userid = $userid;
        $this->amount = (int)HanziUserTask::getFinishedWorkCountToday($userid, $this->type);
        return true;

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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'userid' => Yii::t('common', '用户名'),
            'type' => Yii::t('common', '类型'),
            'amount' => Yii::t('common', '完成量'),
            'content' => Yii::t('common', '日志'),
            'created_at' => Yii::t('common', '日期'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }
}
