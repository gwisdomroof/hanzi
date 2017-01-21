<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%hanzi_set}}".
 *
 * @property string $id
 * @property integer $source
 * @property integer $type
 * @property string $word
 * @property string $pic_name
 * @property integer $nor_var_type
 * @property string $belong_standard_word_code
 * @property string $standard_word_code
 * @property string $position_code
 * @property integer $duplicate
 * @property string $duplicate_id
 * @property integer $frequency
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
class HanziSet extends \yii\db\ActiveRecord
{
    // 字头来源
    const SOURCE_HANZI_PARTS = 0;
    const SOURCE_UNICODE = 1;
    const SOURCE_TAIWAN = 2;
    const SOURCE_HANYU = 3;
    const SOURCE_GAOLI = 4;
    const SOURCE_DUNHUANG = 5;
    const SOURCE_OTHER = 0;

    // 正异类型
    const TYPE_NORMAL_PURE = 0; # 纯正字
    const TYPE_VARIANT_NARROW = 1; # 狭义异体字
    const TYPE_NORMAL_WIDE = 2;  # 广义且正字
    const TYPE_VARIANT_WIDE = 3;    # 广义异体字
    const TYPE_VARIANT_NORMAL = 4;    # 狭义且正字
    const TYPE_SPECIAL_VARIANT = 5;    # 特定异体字
    const TYPE_SPECIAL_NORMAL = 6;    # 特定且正字
    const TYPE_OTHER_ERRORS = 7;    # 误刻误印
    const TYPE_OTHER_NOT_SELECTED = 8;    # 其他不入库类型
    const TYPE_OTHER_SELECTED = 9;    # 其他入库类型
    #
    // 是否难字
    const HARD_TRUE = 1;
    const HARD_FALSE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_set}}';
    }

    /**
     * 获取model对应的原始网站的url
     * @inheritdoc
     */
    public function getOriginUrl($position)
    {
        if ($this->source == self::SOURCE_TAIWAN) {
            $type = strtolower(substr($position, 0, 1)); 
            $fuluzi = json_decode(Yii::$app->get('keyStorage')->get('frontend.tw-fuluzi', null, false));
            $position0 = explode('-', $position)[0];
            if (!in_array($position, $fuluzi)) {    # 附录字
                return "http://dict.variants.moe.edu.tw/yiti".$type."/fr".$type."/fr".$position0.".htm";
            } else {
                return "http://dict.variants.moe.edu.tw/yiti".$type."/ur".$type."/ur".$position0.".htm";
            }
        } elseif($this->source == self::SOURCE_GAOLI) {
            $busu = $this->radical;
            $busuCnt = (int)HanziSet::getStocks($this->radical);
            $restCnt = (int)$this->stocks - $busuCnt;
            return "http://kb.sutra.re.kr/ritk_eng/etc/chinese/chineseBitSearch.do?busu=" . $busu ."&busuCnt=".$busuCnt."&restCnt=".$restCnt;
        } else {
            return '';
        }
    }

    /**
     * 获取param对应汉字的笔画
     * @inheritdoc
     */
    public static function getStocks($param) {
        $models = HanziSet::find()->orderBy('id')->where(['word' => $param])->all();
        foreach ($models as $model) {
            if (!empty($model->min_stroke)) {
                return $model->min_stroke;
            }
        }
    }

    /**
     * 获取model对应的本地网站的url
     * @inheritdoc
     */
    public function getUrl()
    {
        $urls = [];
        if ($this->source == self::SOURCE_TAIWAN) {
            $positions = empty($this->standard_word_code) ? $this->position_code : $this->standard_word_code . ";" . $this->position_code;
            $positions = explode(';', $positions);
            foreach ($positions as $position) {
                $position = ltrim($position, "#");
                $urls[$position] = Url::toRoute(['/hanzi-dict/taiwan', 'param' => $position]);
            }

        } elseif($this->source == self::SOURCE_HANYU) {
            $position = $this->position_code;
            $page = explode('-', $position)[1];
            $page =  str_pad($page, 4, '0', STR_PAD_LEFT);
            $urls[$position] = Url::toRoute(['/hanzi-dict/hanyu', 'param' => $page]);

        } elseif($this->source == self::SOURCE_GAOLI) {
            $position = $this->belong_standard_word_code;
            $urls[$position] = Url::toRoute(['/hanzi-dict/gaoli', 'param' => $this->belong_standard_word_code]);
        } elseif($this->source == self::SOURCE_DUNHUANG) {
            $position = (int)$this->position_code;
            $urls[$position] = Url::toRoute(['/hanzi-dict/dunhuang', 'param' => $position]);
            
        } 
        return $urls;
    }

    /**
     * @inheritdoc
     */
    public static function getPicturePath($soure, $pic_name)
    {
        switch ($soure) {
            case self::SOURCE_TAIWAN:
                $gapDir = substr($pic_name, 0, 2);
                return "/img/hanzi/tw/$gapDir/$pic_name.png";
            
            case self::SOURCE_HANYU:
                return "/img/hanzi/hy/$pic_name.png";

            case self::SOURCE_GAOLI:
                if (strpos($pic_name, '1') !== false) {
                    return "/img/hanzi/gl/variant1/$pic_name.png";
                } elseif (strpos($pic_name, '2') !== false) {
                    return "/img/hanzi/gl/variant2/$pic_name.png";
                } else {
                    return "/img/hanzi/gl/standard/$pic_name.png";
                }

            case self::SOURCE_DUNHUANG:
                return "/img/hanzi/dh/$pic_name.png";
            
            case self::SOURCE_OTHER:
                return "/img/hanzi/th/$pic_name.png";

            default:
                return false;
        }

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'type', 'nor_var_type', 'frequence', 'duplicate', 'max_stroke', 'min_stroke', 'bhard', 'created_at', 'updated_at'], 'integer'],
            [['word', 'radical', 'structure'], 'string', 'max' => 8],
            [['pic_name', 'belong_standard_word_code', 'standard_word_code', 'pinyin'], 'string', 'max' => 64],
            [['position_code', 'duplicate_id', 'zhengma', 'wubi',  'similar_stock'], 'string', 'max' => 128],
            [['min_split', 'deform_split', 'stock_serial', 'remark'], 'string', 'max' => 256],
            [['max_split', 'mix_split'], 'string', 'max' => 512],
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
            'id' => Yii::t('frontend', 'ID'),
            'source' => Yii::t('frontend', '来源'),
            'type' => Yii::t('frontend', '类型'),
            'word' => Yii::t('frontend', '文字'),
            'pic_name' => Yii::t('frontend', '图片'),
            'frequence' => Yii::t('frontend', '频率'),
            'nor_var_type' => Yii::t('frontend', '正异关系'),
            'belong_standard_word_code' => Yii::t('frontend', '所属正字'),
            'standard_word_code' => Yii::t('frontend', '兼正字号'),
            'position_code' => Yii::t('frontend', '位置编码'),
            'duplicate' => Yii::t('frontend', '是否重复'),
            'duplicate_id' => Yii::t('frontend', '重复ID'),
            'pinyin' => Yii::t('frontend', '拼音'),
            'radical' => Yii::t('frontend', '部首'),
            'stocks' => Yii::t('frontend', '笔画'),
            'zhengma' => Yii::t('frontend', '郑码'),
            'wubi' => Yii::t('frontend', '五笔'),
            'structure' => Yii::t('frontend', '结构'),
            'bhard' => Yii::t('frontend', '是否难字'),
            'min_split' => Yii::t('frontend', '最小拆分'),
            'deform_split' => Yii::t('frontend', '调笔拆分'),
            'similar_stock' => Yii::t('frontend', '相似部件'),
            'max_split' => Yii::t('frontend', '最大拆分'),
            'mix_split' => Yii::t('frontend', '混合拆分'),
            'stock_serial' => Yii::t('frontend', '部件序列'),
            'remark' => Yii::t('frontend', '备注'),
            'created_at' => Yii::t('frontend', '创建时间'),
            'updated_at' => Yii::t('frontend', '更新时间'),
        ];
    }


    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function norVarTypes($bshort = true)
    {
        if ($bshort) {
            return [
                self::TYPE_NORMAL_PURE => Yii::t('frontend', '正字'),
                self::TYPE_VARIANT_NARROW => Yii::t('frontend', '狭义异体字'),
                self::TYPE_VARIANT_NORMAL => Yii::t('frontend', '狭义且正字'),
                self::TYPE_VARIANT_WIDE => Yii::t('frontend', '广义异体字'),
                self::TYPE_NORMAL_WIDE => Yii::t('frontend', '广义且正字'),
                self::TYPE_SPECIAL_VARIANT => Yii::t('frontend', '特定异体字'),
                self::TYPE_SPECIAL_NORMAL => Yii::t('frontend', '特定且正字'),
                self::TYPE_OTHER_ERRORS => Yii::t('frontend', '误刻误印'),
                self::TYPE_OTHER_NOT_SELECTED => Yii::t('frontend', '其他不入库类型'),
                self::TYPE_OTHER_SELECTED => Yii::t('frontend', '其他入库类型')
            ];
        } else {
            return [
                self::TYPE_NORMAL_PURE => Yii::t('frontend', '正字'),
                self::TYPE_VARIANT_NARROW => Yii::t('frontend', '狭义异体字'),
                self::TYPE_VARIANT_NORMAL => Yii::t('frontend', '狭义异体字兼正字'),
                self::TYPE_VARIANT_WIDE => Yii::t('frontend', '广义异体字'),
                self::TYPE_NORMAL_WIDE => Yii::t('frontend', '广义异体字兼正字'),
                self::TYPE_SPECIAL_VARIANT => Yii::t('frontend', '特定异体字'),
                self::TYPE_SPECIAL_NORMAL => Yii::t('frontend', '特定且正字'),
                self::TYPE_OTHER_ERRORS => Yii::t('frontend', '误刻误印'),
                self::TYPE_OTHER_NOT_SELECTED => Yii::t('frontend', '其他不入库类型'),
                self::TYPE_OTHER_SELECTED => Yii::t('frontend', '其他入库类型')

            ];
        }
    }


    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function sources()
    {
        return [
            self::SOURCE_UNICODE => Yii::t('frontend', 'Unicode'),
            self::SOURCE_TAIWAN => Yii::t('frontend', '台湾异体字字典'),
            self::SOURCE_HANYU => Yii::t('frontend', '汉语大字典'),
            self::SOURCE_GAOLI => Yii::t('frontend', '高丽异体字字典'),
            self::SOURCE_DUNHUANG => Yii::t('frontend', '敦煌俗字典'),
            self::SOURCE_OTHER => Yii::t('frontend', '其他')
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function hards()
    {
        return [
            self::HARD_TRUE => Yii::t('frontend', '是'),
            self::HARD_FALSE => Yii::t('frontend', '否'),
        ];
    }
}
