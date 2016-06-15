<?php
namespace common\components\hanziPart;

use yii\web\AssetBundle;

/**
 * HanziPartAsset
 *
 * @author Xiandu
 */
class HanziPartAsset extends AssetBundle
{
    public $js = [
        'hanziPart.js',
        'hanziPart-search.js',
    ];
    public $css = [
        'hanziPart.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }

}