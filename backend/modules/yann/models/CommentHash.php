<?php
namespace app\modules\yann\models;

use Yii;
use yii\db\ActiveRecord;

class CommentHash extends ActiveRecord {
    public static function tableName() {
        return '{{%comment_hash}}';
    }

    public function getId($url) {
        $hash = md5($url);
        $model = self::find()->where(['hash' => $hash])->one();
        if ($model) {
            return $model->id;
        } else {
            return $this->setHash($url);
        }
    }

    public function getHash($url) {
        $hash = md5($url);
        $model = self::findOne($hash);
        return $model->hash;
    }

    public function setHash($url) {
        $model = new CommentHash();
        $model->hash = md5($url);
        $model->url = $url;
        $model->created_at = time();
        $model->updated_at = time();
        $model->save();
        return $model->id;
    }
}