<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\HanziImage;

class HanziImageController extends Controller
{
    public function actionTransfer()
    {
        $path = Yii::getAlias('@image');
        $pathTw = $path . '/tw';
        $files = $this->getFiles($pathTw);

        foreach ($files as $key => $filename) {
            $model = new HanziImage();
            $imgbinary = fread(fopen($filename, "r"), filesize($filename));
            $filetype = substr(strrchr($filename, '.'), 1);

            $model->source = 1;
            $model->name = $key;
            $model->value = 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);

            if(!$model->save()) {
                var_dump($model->getErrors());
                return static::EXIT_CODE_ERROR;
            }
        }

        return static::EXIT_CODE_NORMAL;
    }

    /**
     * [getFiles description]
     * @param  [type] $dir [description]
     * @return [type]      [description]
     */
    private function getFiles($dir)
    {
        $files = [];
     
        if(!is_dir($dir)) {
            return $files;
        }
     
        $handle = opendir($dir);
        if($handle) {
            while(false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    $filename = $dir . "/"  . $file;
                    if (is_file($filename) ) {
                        $suffix = substr(strrchr($file, '.'), 1);
                        if ($suffix == "png" || $suffix == "jpg")
                            $files[$file] = $filename;
                    } else {
                        $files = array_merge($files, $this->getFiles($filename));
                    }
                }
            }   //  end while
            closedir($handle);
        }
        return $files;
    }


}
