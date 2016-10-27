<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\HanziTask;

/**
 * This is the model class for table "{{%work_package}}".
 *
 * @property string $id
 * @property integer $userid
 * @property integer $type
 * @property integer $volume
 * @property integer $daily_schedule
 * @property integer $expected_date
 * @property string $progress
 * @property integer $created_at
 * @property integer $updated_at
 */
class WorkPackage extends \yii\db\ActiveRecord
{
    public $selfSchedule;   #自定义日工作量

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%work_package}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'userid', 'volume', 'daily_schedule', 'selfSchedule', 'expected_date', 'progress', 'created_at', 'updated_at'], 'integer'],
            [['daily_schedule'], 'shouldDivideByFive'],
            [['volume', 'daily_schedule'], 'required'],
            ['type', 'oneWorkEachType']
        ];
    }

    /**
     *  自定义验证
     */
    public function shouldDivideByFive($attribute, $params)
    {
        if ($this->daily_schedule % 5 !== 0) {
            $this->addError($attribute, "日计划必须是5的倍数。");
        }
    }

    public function oneWorkEachType($attribute, $params)
    {
        if ($this->isNewRecord) {
            $exist = WorkPackage::find()->where(['type' => $this->type, 'userid' => Yii::$app->user->id])
                ->andWhere('progress < volume')
                ->exists();
            if ($exist) {
                $typeInfo = WorkPackage::types()["{$this->type}"];
                $this->addError($attribute, "一次只能领取一个{$typeInfo}工作包，请先完成未完成的工作包。");
            }
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
     * 更新进度条
     * Returns user statuses list
     * @return array|mixed
     */
    public static function updateProgress($id, $progress)
    {
        $model = WorkPackage::findOne($id);
        if (!empty($model)) {
            $model->progress = $progress;
            $model->update();
        } else {
            throw new \yii\db\Exception("未找到id{$id}对应的数据。");
        }

    }

    /**
     * 获取当前工作包
     * Returns user statuses list
     * @return array|mixed
     */
    public static function getCurWorkPackage($userid, $type)
    {
        return WorkPackage::find()->where(['userid' => $userid, 'type' => $type])
            ->andWhere('progress < volume')
            ->orderBy('id')
            ->one();
    }

    /**
     * 获取当前进度
     * Returns user statuses list
     * @return array|mixed
     */
    public function getCurrentProgress()
    {
        if ($this->type == HanziTask::TYPE_DEDUP)
            return \common\models\HanziTask::getFinishedWorkCountFrom($this->userid, $this->type, $this->created_at);
        else
            return \common\models\HanziUserTask::getFinishedWorkCountFrom($this->userid, $this->type, $this->created_at);
    }

    /**
     * 获取今日完成的工作量
     * Returns user statuses list
     * @return array|mixed
     */
    public function getFinishedToday()
    {
        if ($this->type == HanziTask::TYPE_DEDUP)
            return \common\models\HanziTask::getFinishedWorkCountToday($this->userid, $this->type, $this->created_at);
        else
            return \common\models\HanziUserTask::getFinishedWorkCountToday($this->userid, $this->type);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // 设置日工作量
            if ($this->selfSchedule) {
                $this->daily_schedule = $this->selfSchedule;
            }
            // 设置默认userid
            $this->userid = Yii::$app->user->id;
            // 根据工作总量和每日工作量，计算完成日期
            $add = (int)($this->volume / $this->daily_schedule);
            $time = strtotime("+{$add} day");
            $this->expected_date = mktime(23, 59, 59, date('m', $time), date('d', $time), date('Y', $time));
            if ($this->isNewRecord)
                $this->progress = 0;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 状态类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function types()
    {
        return [
            HanziTask::TYPE_SPLIT => Yii::t('common', '台湾异体字拆字'),
            HanziTask::TYPE_GAOLI_SPLIT => Yii::t('common', '高丽异体字拆字'),
            HanziTask::TYPE_DEDUP => Yii::t('common', '高丽台湾异体字去重'),
            HanziTask::TYPE_INPUT => Yii::t('common', '异体字录入')
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
            'id' => Yii::t('common', 'ID'),
            'userid' => Yii::t('common', '用户'),
            'type' => Yii::t('common', '任务类型'),
            'volume' => Yii::t('common', '总工作量'),
            'daily_schedule' => Yii::t('common', '每日计划'),
            'expected_date' => Yii::t('common', '预计完成日'),
            'progress' => Yii::t('common', '进　度'),
            'created_at' => Yii::t('common', '领取日'),
            'updated_at' => Yii::t('common', '完成日'),
        ];
    }

}
