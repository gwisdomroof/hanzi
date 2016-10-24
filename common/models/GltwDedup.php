<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%hanzi_gltw_dedup}}".
 *
 * @property string $id
 * @property string $gaoli
 * @property string $unicode
 * @property integer $relation
 * @property integer $status
 * @property integer $frequency
 * @property integer $is_occupied
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class GltwDedup extends \yii\db\ActiveRecord
{
    // 去重的状态
    const STATUS_NONEED = 0;   # 不须去重
    const STATUS_INITIAL = 1;   # 尚未去重
    const STATUS_DEDUPED = 2;   # 已经去重尚未审核
    const STATUS_CHECKED = 3;   # 已经回查
    const STATUS_CONFIRMED = 4;   # 已经审核

    // 去重的阶段
    const STAGE_FIRST = 1;   # 去重
    const STAGE_SECOND = 2;   # 回查
    const STAGE_THIRD = 3;   # 审核

    // 高丽正字与Unicode的关系
    const RELATION_SAME = 1;   # 高丽正字所用的编码与Unicode相同
    const RELATION_SIMILAR = 2;   # 高丽正字所用的编码与Unicode相似
    const RELATION_DIFF = 3;   # 高丽正字所用的编码与Unicode不同
    const RELATION_EMPTY = 4;   # 高丽正字所用的编码无Unicode与之对应

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_gltw_dedup}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gaoli', 'created_at', 'updated_at'], 'required'],
            [['status', 'relation', 'frequency', 'is_occupied', 'created_at', 'updated_at'], 'integer'],
            [['gaoli', 'unicode'], 'string', 'max' => 32],
            [['remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'gaoli' => Yii::t('frontend', '高丽'),
            'unicode' => Yii::t('frontend', 'Unicode'),
            'status' => Yii::t('frontend', '状态'),
            'relation' => Yii::t('frontend', '与Unicode的关系'),
            'frequency' => Yii::t('frontend', '重复个数'),
            'is_occupied' => Yii::t('frontend', '是否被占用'),
            'remark' => Yii::t('frontend', '备注'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * Returns statuses list
     * @return array|mixed
     */
    public static function relations()
    {
        return [
            self::RELATION_SAME => Yii::t('frontend', '形码均相同'),
            self::RELATION_SIMILAR => Yii::t('frontend', '形似码相同'),
            self::RELATION_DIFF => Yii::t('frontend', '形同码不同'),
            self::RELATION_EMPTY => Yii::t('frontend', '无相同字形'),
        ];
    }

    /**
     * Returns statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NONEED => Yii::t('frontend', '不须去重'),
            self::STATUS_INITIAL => Yii::t('frontend', '尚未去重'),
            self::STATUS_DEDUPED => Yii::t('frontend', '已经去重'),
            self::STATUS_CHECKED => Yii::t('frontend', '已经回查'),
            self::STATUS_CONFIRMED => Yii::t('frontend', '已经审核')
        ];
    }

    /**
     * 获取供去重的新页面
     * @param string $id
     * @return mixed
     */
    public static function getNewPage()
    {
        $seq = (int)Yii::$app->get('keyStorage')->get('frontend.current-dedup-stage', null, false);
        $seqStatusMap = [
            '1' => GltwDedup::STATUS_INITIAL,
            '2' => GltwDedup::STATUS_DEDUPED,
            '3' => GltwDedup::STATUS_CHECKED
        ];
        $status = $seqStatusMap[$seq];

        // 寻找当前阶段未被占用的最小ID
        $model = GltwDedup::find()
            ->where(['status' => $status])
            ->andWhere('is_occupied = 0 OR is_occupied is null')
            ->orderBy('id')
            ->one();
        if (empty($model)) {
            throw new \yii\db\Exception("未找到合适的页码，请联系管理员。");
        }

        // 分配id，设置该id状态为被占用
        $model->is_occupied = 1;
        $model->save();

        return $model->id;
    }
}
