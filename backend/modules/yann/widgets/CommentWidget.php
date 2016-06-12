<?php
namespace app\modules\yann\widgets;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\base\Widget;

/**
 * Class CommentWidget
 * @package app\modules\yann\widget
 */
class CommentWidget extends Widget {
    public $username = null;

    public function init() {

    }

    public function run() {
        return Html::encode($this->username);
    }
}
