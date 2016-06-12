<?php

namespace app\modules\yann;

/**
 * comment module definition class
 */
class Module extends \yii\base\Module {
    public $defaultRoute = 'manage';
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\yann\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // custom initialization code goes here
    }
}
