<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\HanziTask;

/**
 * This is the model class for table "{{%common_page}}".
 *
 * @property string $id
 * @property integer $page
 * @property integer $task_type
 * @property integer $seq
 * @property integer $flag
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class CommonPage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_page}}';
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
            [['page', 'task_type', 'seq', 'flag', 'created_at', 'updated_at'], 'integer'],
            [['remark'], 'string', 'max' => 128],
            ['page', function ($attribute, $params) {
                // 1. create, current page is not allowed duplicated except the record is canceled
                $query = CommonPage::find()
                    ->where(['page' => $this->page, 'task_type' => $this->task_type, 'seq' => $this->seq]);
                // 2. update, except current id
                if (!empty($this->id)) {
                    $query->andFilterWhere(['!=', 'id', $this->id]);
                }
                if ($query->exists()) {
                    $this->addError('page', '该页面已分配。');
                    return false;
                }
                return true;
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->task_type == HanziTask::TYPE_SPLIT)
                $this->seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
            elseif ($this->task_type == HanziTask::TYPE_INPUT)
                $this->seq = Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'page' => Yii::t('frontend', '页码'),
            'task_type' => Yii::t('frontend', '任务类型'),
            'seq' => Yii::t('frontend', '阶段'),
            'flag' => Yii::t('frontend', '标志'),
            'remark' => Yii::t('frontend', 'Remark'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }
}
