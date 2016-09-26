<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%lq_variant}}".
 *
 * @property string $id
 * @property integer $source
 * @property integer $type
 * @property integer $ori_pic_name
 * @property string $word
 * @property string $pic_name
 * @property integer $nor_var_type
 * @property string $belong_standard_word_code
 * @property string $standard_word_code
 * @property string $position_code
 * @property integer $duplicate
 * @property string $duplicate_id
 * @property integer $frequence
 * @property string $sutra_ids
 * @property integer $bconfirm
 * @property string $pinyin
 * @property string $radical
 * @property integer $stocks
 * @property string $zhengma
 * @property string $wubi
 * @property string $structure
 * @property integer $bhard
 * @property string $min_split
 * @property string $deform_split
 * @property string $similar_stock
 * @property string $max_split
 * @property string $mix_split
 * @property string $stock_serial
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class LqVariant extends HanziSet
{
    // 来源
    const SOURCE_TH = 0;    # other
    const SOURCE_UN = 1;    # unicode
    const SOURCE_TW = 2;    # taiwan
    const SOURCE_HY = 3;    # hanyu
    const SOURCE_GL = 4;    # gaoli
    const SOURCE_SZ = 5;    # dunhuang
    const SOURCE_QS = 6;
    const SOURCE_PL = 7;
    const SOURCE_HW = 8;
    const SOURCE_YL = 9;
    const SOURCE_JX = 10;
    const SOURCE_QL = 11;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lq_variant}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['bconfirm'], 'integer'],
            [['ori_pic_name'], 'string', 'max' => 128],
            [['sutra_ids'], 'string', 'max' => 256]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'pic_name' => Yii::t('common', '编号'),
            'ori_pic_name' => Yii::t('common', '图片名'),
            'sutra_ids' => Yii::t('common', '经字号'),
            'bconfirm' => Yii::t('common', '是否存疑'),
        ]);
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function sources()
    {
        return [
            self::SOURCE_UN => Yii::t('frontend', 'UN'),
            self::SOURCE_TW => Yii::t('frontend', 'TW'),
            self::SOURCE_HY => Yii::t('frontend', 'HY'),
            self::SOURCE_GL => Yii::t('frontend', 'GL'),
            self::SOURCE_SZ => Yii::t('frontend', 'SZ'),
            self::SOURCE_QS => Yii::t('frontend', '磧砂'),
            self::SOURCE_PL => Yii::t('frontend', '毗盧'),
            self::SOURCE_HW => Yii::t('frontend', '洪武'),
            self::SOURCE_YL => Yii::t('frontend', '永樂'),
            self::SOURCE_JX => Yii::t('frontend', '嘉興'),
            self::SOURCE_QL => Yii::t('frontend', '乾隆'),
        ];
    }

    /**
     * 获取图片路径
     * 图片名中第一个字为图片的上一级目录；
     * 如果是早期贤保法师的图片，则图片名需要去掉第一个字；否则，不需要去掉。
     * @inheritdoc
     */
    public function getLqPicturePath()
    {
        $normal = !empty($this->ori_pic_name) ? mb_substr($this->ori_pic_name, 0, 1, 'utf8') : 'A';
        $realPicName = $this->ori_pic_name;
        #
        if (!preg_match("/^{$normal}lq\d+.(jpg|png)$/", $realPicName)) {
            $realPicName = substr($realPicName, strlen($normal));
        }
        return '/' . LqVariantCheck::$imageBasePath . "{$normal}/{$realPicName}";
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function addVariantFromCheck($lqVariantCheck)
    {
        $query = LqVariant::find()
            ->orFilterWhere(['like', 'pic_name', $lqVariantCheck->variant_code])
            ->orFilterWhere(['like', 'ori_pic_name', $lqVariantCheck->pic_name]);
        $variant = $query->one();
        if (!empty($variant)) {
            $bChanged = self::loadFromCheck($variant, $lqVariantCheck);
            if ($bChanged && !$variant->save()) {
                throw new \yii\db\Exception("数据保存有误。");
            }

        } else {
            $variant = new LqVariant();
            self::loadFromCheck($variant, $lqVariantCheck);
            if (!$variant->save()) {
                throw new \yii\db\Exception("数据保存有误。");
            }
        }
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function deleteVariantFromCheck($lqVariantCheck)
    {
        $query = LqVariant::find()
            ->orFilterWhere(['like', 'pic_name', $lqVariantCheck->variant_code])
            ->orFilterWhere(['like', 'ori_pic_name', $lqVariantCheck->pic_name]);
        $variant = $query->one();
        if (!empty($variant)) {
            $variant->delete();
        }
    }

    /**
     * @return boolean
     */
    public static function loadFromCheck(&$lqVariant, &$lqVariantCheck)
    {
        $bChanged = false;
        if ($lqVariant->source != $lqVariantCheck->source) {
            $lqVariant->source = $lqVariantCheck->source;
            $bChanged = true;
        }
        # lqVariant的pic_name实际是lqVariantCheck的variant_code
        if ($lqVariant->pic_name != $lqVariantCheck->variant_code) {
            $lqVariant->pic_name = $lqVariantCheck->variant_code;
            $bChanged = true;
        }
        # lqVariant的ori_pic_name实际是lqVariantCheck的pic_name
        if ($lqVariant->ori_pic_name != $lqVariantCheck->pic_name) {
            $lqVariant->ori_pic_name = $lqVariantCheck->pic_name;
            $bChanged = true;
        }
        if ($lqVariant->nor_var_type != $lqVariantCheck->nor_var_type) {
            $lqVariant->nor_var_type = $lqVariantCheck->nor_var_type;
            $bChanged = true;
        }
        if ($lqVariant->belong_standard_word_code != $lqVariantCheck->belong_standard_word_code) {
            $lqVariant->belong_standard_word_code = $lqVariantCheck->belong_standard_word_code;
            $bChanged = true;
        }
        return $bChanged;
    }
}
