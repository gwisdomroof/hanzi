<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%lq_variant_check}}".
 *
 * @property string $id
 * @property integer $userid
 * @property integer $source
 * @property string $pic_name
 * @property string $variant_code
 * @property string $belong_standard_word_code
 * @property integer $nor_var_type
 * @property integer $level
 * @property integer $bconfirm
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class LqVariantCheck extends \yii\db\ActiveRecord
{
    // 难易等级
    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;
    const LEVEL_FOUR = 4;
    const LEVEL_FIVE = 5;
    const LEVEL_SIX = 6;
    const LEVEL_SEVEN = 7;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    public static $imageBasePath = 'img/FontImage/';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lq_variant_check}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'source', 'nor_var_type', 'level', 'bconfirm', 'created_at', 'updated_at'], 'integer'],
            [['pic_name', 'variant_code', 'origin_standard_word_code', 'belong_standard_word_code'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 128],
//            [['belong_standard_word_code'], 'required'],
//            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            ['imageFile', 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg',
                'minWidth' => 30, 'maxWidth' => 1000,
                'minHeight' => 30, 'maxHeight' => 1000,
            ],
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

    public function getPicPath()
    {
        $normal = !empty($this->origin_standard_word_code) ? $this->origin_standard_word_code : mb_substr($this->belong_standard_word_code, 0, 1, 'utf8');
        return '/' . self::$imageBasePath . "{$normal}/{$this->pic_name}";
    }

    /**
     * 获取数据库中正字$normal下最大的编号值
     * @return integer
     */
    public function getMaxId($normal)
    {
        $existNames = LqVariantCheck::find()->select('pic_name')
            ->where("pic_name ~ '^{$normal}\d+.(jpg|png)$'")
            ->asArray()
            ->all();
        $max = 0;
        foreach ($existNames as $name) {
            $num = (int)preg_replace("({$normal}|.jpg|.png)", '', $name['pic_name']);
            $max = $max < $num ? $num : $max;
        }
        return $max;
    }

    public function upload()
    {
        if ($this->validate()) {

            $normal = 'A'; # 正字为空时，默认存放在A目录下
            if (!empty($this->belong_standard_word_code)) {
                $normal = mb_substr($this->belong_standard_word_code, 0, 1, 'utf8');
            }
            # 新建数据，或者更新数据但是正字发生变化时，图片名也发生改变
            $oldPicName = $this->pic_name;
            if ($this->isNewRecord || (!$this->isNewRecord && $this->origin_standard_word_code != $normal)) {
                $maxId = $this->getMaxId($normal);
                $this->pic_name = $normal . ++$maxId . '.' . $this->imageFile->extension;
                $this->origin_standard_word_code = $normal;
            }
            $path = self::$imageBasePath . $normal . '/';
            $fullFileName = $path . $this->pic_name;
            # 如果是windows服务器，则进行一层转换
            if (DIRECTORY_SEPARATOR == '\\') {
                $path = iconv('utf-8', 'gbk', $path);
                $fullFileName = iconv('utf-8', 'gbk', $fullFileName);
            }
            if (!is_dir($path)) {
                mkdir($path);
            }
            $this->imageFile->saveAs($fullFileName);
//            $this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            # 更新数据且图片名发生变化时，删除之前的数据
            if (!$this->isNewRecord && $oldPicName != $this->pic_name) {
                $dir = mb_substr($oldPicName, 0, 1, 'utf8');
                if (DIRECTORY_SEPARATOR == '\\') {
                    unlink(iconv('utf-8', 'gbk', self::$imageBasePath . "{$dir}/{$oldPicName}"));
                } else {
                    unlink(self::$imageBasePath . "{$dir}/{$oldPicName}");
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function beforeSave($insert)
    {
        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');
        if (!empty($this->imageFile)) {
            if (!$this->upload()) {
                $this->addError('imageFile', '文件保存错误。');
                return false;
            }
        }
        if ($this->isNewRecord) {
            $this->userid = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'userid' => Yii::t('common', '用户名'),
            'source' => Yii::t('common', '来源'),
            'pic_name' => Yii::t('common', '图片名'),
            'imageFile' => Yii::t('common', '图片'),
            'variant_code' => Yii::t('common', '异体字编号'),
            'origin_standard_word_code' => Yii::t('common', '原属正字'),
            'belong_standard_word_code' => Yii::t('common', '所属正字'),
            'nor_var_type' => Yii::t('common', '正异类型'),
            'level' => Yii::t('common', '等级'),
            'bconfirm' => Yii::t('common', '是否确定'),
            'remark' => Yii::t('common', '备注'),
            'created_at' => Yii::t('common', '提交时间'),
            'updated_at' => Yii::t('common', '更新时间'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function isNew()
    {
        if (!empty($this->variant_code) || !empty($this->nor_var_type)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function levels()
    {
        return [
            self::LEVEL_ONE => Yii::t('frontend', '一'),
            self::LEVEL_TWO => Yii::t('frontend', '二'),
            self::LEVEL_THREE => Yii::t('frontend', '三'),
            self::LEVEL_FOUR => Yii::t('frontend', 'A'),
            self::LEVEL_FIVE => Yii::t('frontend', 'B'),
            self::LEVEL_SIX => Yii::t('frontend', 'C'),
            self::LEVEL_SEVEN => Yii::t('frontend', 'D')
        ];
    }

}
