<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%hanzi_parts}}".
 *
 * @property string $id
 * @property integer $part_type
 * @property string $part_char
 * @property string $part_pic_id
 * @property integer $src_chs_lib
 * @property integer $src_gb13000
 * @property integer $src_old_lqhanzi
 * @property integer $src_feijinchang
 * @property integer $src_hujingyu
 * @property integer $src_lqhanzi
 * @property integer $lqhanzi_sn
 * @property integer $is_redundant
 * @property integer $frequency_zhzk
 * @property integer $frequency
 * @property integer $is_split_part
 * @property integer $is_search_part
 * @property string $replace_parts
 * @property integer $strokes
 * @property string $stroke_order
 * @property string $remark
 * @property integer $c_t
 * @property integer $u_t
 */
class HanziParts extends \yii\db\ActiveRecord
{
    public $source;
    public $batch_part_chars;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_parts}}';
    }


    /**
     * @inheritdoc
     */
    public static function sources()
    {
        return [
            '1' => 'ZHZK',
            '2' => 'GB13000.1',
            '3' => '拆字网',
            '4' => '费锦昌',
            '5' => '胡敬禹',
            '6' => '龙泉字库',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function sourceFields()
    {
        return [
            '1' => 'src_chs_lib',
            '2' => 'src_gb13000',
            '3' => 'src_old_lqhanzi',
            '4' => 'src_feijinchang',
            '5' => 'src_hujingyu',
            '6' => 'src_lqhanzi',
        ];
    }

    public function mbstringToArray($str, $charset)
    {
        $array = [];
        $strlen = mb_strlen($str);
        while ($strlen) {
            $array[] = mb_substr($str, 0, 1, $charset);
            $str = mb_substr($str, 1, $strlen, $charset);
            $strlen = mb_strlen($str);
        }
        return $array;
    }

    /**
     * @inheritdoc
     */
    public function batchRegister()
    {
        $partChars = trim($this->batch_part_chars);
        $partChars = str_replace([' ', '\n'], '', $partChars);
        $partCharsArray = $this->mbstringToArray($partChars, 'UTF-8');
        $partChars = "'" . implode("', '", $partCharsArray) . "'";

        $existParts = HanziParts::find()->select('part_char')->where("part_char in ({$partChars})")->asArray()->all();
        $existPartsArray = [];
        foreach ($existParts as $e) {
            $existPartsArray[] = $e['part_char'];
        }
        $existPartChars = "'" . implode("', '", $existPartsArray) . "'";

        $notExistPartArray = array_diff($partCharsArray, $existPartsArray);

        $msg = null;
        if (!empty($this->source)) {
            $source = self::sourceFields()[$this->source];
            HanziParts::updateAll([$source => 1], "part_char in ({$existPartChars})");
        } else {
            $msg = '请选择部件来源……';
        }

        return [
            'existed' => implode("", $existPartsArray),
            'notExisted' => implode("", $notExistPartArray),
            'msg' => $msg
        ];
    }


    /**
     * @inheritdoc
     */
    public function batchSave()
    {
        $partChars = trim($this->batch_part_chars);
        $partChars = str_replace([' ', '\n'], '', $partChars);
        $partCharsArray = $this->mbstringToArray($partChars, 'UTF-8');
        $partChars = "'" . implode("', '", $partCharsArray) . "'";

        // 注册已存在的部件
        $existParts = HanziParts::find()->select('part_char')->where("part_char in ({$partChars})")->asArray()->all();
        $existPartsArray = [];
        foreach ($existParts as $e) {
            $existPartsArray[] = $e['part_char'];
        }
        $existPartChars = "'" . implode("', '", $existPartsArray) . "'";

        $source = self::sourceFields()[$this->source];
        HanziParts::updateAll([$source => 1], "part_char in ({$existPartChars})");

        // 新增不存在的部件
        $notExistPartArray = array_diff($partCharsArray, $existPartsArray);
        $failParts = [];
        $succeedParts = [];
        foreach ($notExistPartArray as $e) {
            $model = new HanziParts();
            $model->part_char = $e;
            $model->$source = 1;
            if (!$model->save())
                $failParts[] = $e;
            else
                $succeedParts[] = $e;
        }

        return [
            'existed' => implode("", $existPartsArray),
            'succeed' => implode("", $succeedParts),
            'failed' => implode("", $failParts),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['part_type', 'is_redundant', 'frequency_zhzk', 'frequency', 'is_split_part', 'is_search_part', 'strokes', 'c_t', 'u_t', 'source'], 'integer'],
            [['part_char'], 'string', 'max' => 8],
            [['part_pic_id'], 'string', 'max' => 32],
            [['replace_parts', 'stroke_order', 'remark'], 'string', 'max' => 64],
            [['batch_part_chars'], 'string', 'max' => 10240],
            [['part_char', 'part_pic_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'part_type' => Yii::t('frontend', '字形类型'),
            'part_char' => Yii::t('frontend', '文字'),
            'part_pic_id' => Yii::t('frontend', '图片字'),
            'src_chs_lib' => Yii::t('frontend', 'ZHZK'),
            'src_gb13000' => Yii::t('frontend', 'GB13000.1'),
            'src_old_lqhanzi' => Yii::t('frontend', '拆字网'),
            'src_feijinchang' => Yii::t('frontend', '费锦昌'),
            'src_hujingyu' => Yii::t('frontend', '胡敬禹'),
            'src_lqhanzi' => Yii::t('frontend', '龙泉字库'),
            'lqhanzi_sn' => Yii::t('frontend', '龙泉字库序号'),
            'is_redundant' => Yii::t('frontend', '是否冗余'),
            'frequency_zhzk' => Yii::t('frontend', '频率ZHZK'),
            'frequency' => Yii::t('frontend', '频率'),
            'is_split_part' => Yii::t('frontend', '是否拆字部件'),
            'is_search_part' => Yii::t('frontend', '是否检字部件'),
            'replace_parts' => Yii::t('frontend', '代替部件'),
            'strokes' => Yii::t('frontend', '笔画'),
            'stroke_order' => Yii::t('frontend', '笔顺'),
            'remark' => Yii::t('frontend', '备注'),
            'c_t' => Yii::t('frontend', '创建时间'),
            'u_t' => Yii::t('frontend', '修改时间'),
            'source' => Yii::t('frontend', '字形来源'),
            'batch_part_chars' => Yii::t('frontend', '批量部件'),
        ];
    }
}
