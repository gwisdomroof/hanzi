<?php

namespace app\modules\comment;

/**
 * comment module definition class
 */
class Module extends \yii\base\Module {
    public $defaultRoute = 'manage';
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\comment\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // custom initialization code goes here
    }
}
