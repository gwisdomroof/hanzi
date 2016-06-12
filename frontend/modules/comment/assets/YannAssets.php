<?php
namespace app\modules\comment\assets;

use yii\web\AssetBundle;

class YannAssets extends AssetBundle {
    public $sourcePath = '@app/modules/comment/assets';

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