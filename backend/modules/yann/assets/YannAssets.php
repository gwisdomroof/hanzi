<?php
namespace app\modules\yann\assets;

use yii\web\AssetBundle;

class YannAssets extends AssetBundle {
    public $sourcePath = '@app/modules/yann/assets';

    public $css = [
        'css/comment.css'
    ];
    public $js = [
    	'js/vue.min.js',
        'js/comment.js',
    ];

    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}