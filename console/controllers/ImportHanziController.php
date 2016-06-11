<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\HanziSet;
use PHPExcel;
use PHPExcel\IOFactory;

class ImportHanziController extends Controller
{

    /**
     * 修改汉语大字典的数据
     * @return mixed
     */
    public function actionModifyHy()
    {
        // 处理代替字头
        $models = HanziSet::find()->where("word ~ '^[\[X]'")->orderBy("id")->all();
        $picNotExist = [];
        $similarStockArr = [];
        echo count($models).'<br/>';
        foreach ($models as $hanzi) {
            $pos = explode("-", $hanzi->position_code);

            // pic name
            $hanzi->pic_name = $pos[1].'n'.$pos[2];
            
            // pic exist
            $filename = "img/hy/$hanzi->pic_name.png";
            if (!file_exists($filename)) {
                $hanzi->remark = "picture not exist.";
            }

            // similar stock
            if (strpos($hanzi->word, '[') === 0) {
                $similarStock = preg_replace('/[\[\]{}]/', '', $hanzi->word);
                $hanzi->similar_stock = $similarStock;
            }

            // hanzi word
            $hanzi->word = null;

            if (!$hanzi->save()) {
                var_dump($hanzi->getErrors());
                return static::EXIT_CODE_ERROR;
            };

        }
        
        // echo "-------------pic not exist------------<br/>";
        // echo "count:".count($picNotExist)."<br/>";

        // foreach ($picNotExist as $key => $value) {
        //     echo $value . '<br/>';
        // }

        // echo "-------------similar stocks------------<br/>";
        // echo "count:".count($similarStockArr)."<br/>";
        // foreach ($similarStockArr as $key => $value) {
        //     echo $value . '<br/>';
        // }
        
        return static::EXIT_CODE_NORMAL;
    }

    /**
     * 整理img下的图片路径
     * @return mixed
     */
    public function actionPadpic()
    {
        $dir = 'e:/Code/hanzi/frontend/web/img/tw';
        $handle = opendir($dir);
        if($handle) {
            while(false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    $filename = $dir . "/"  . $file;
                    if (is_file($filename) ) {
                        $suffix = substr(strrchr($file, '.'), 1);
                        if ($suffix == "png" || $suffix == "jpg") {
                            $gapDir = substr($file, 0, 2);
                            if (!is_dir($dir . "/"  . $gapDir)) {
                                mkdir($dir . "/"  . $gapDir);
                            }
                            rename("$filename", "$dir/$gapDir/$file");
                        }
                    }
                }
            }   //  end while
            closedir($handle);
        }

        echo "success!";
        return static::EXIT_CODE_NORMAL;

    }
    
    /**
     * 导入汉语大字典数据
     * @return [type] [description]
     */
    public function actionImportHydzd()
    {
        $filename = 'd:/Inbox/hydzd.xls';

        $objPHPExcel = new PHPExcel();
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');  //加载2003的
        $objPHPExcel = $objReader->load($filename);  //载入文件

        // 处理第三、四页
        $zitouArr = [];
        $sheet = $objPHPExcel->getSheet(2); 
        $i = 1;
        foreach ($sheet->getDrawingCollection() as $k => $drawing) {
                $codata = $drawing->getCoordinates(); //得到单元数据 比如G2单元
                // $filename = $drawing->getIndexedFilename();  //文件名
                
                $p = $sheet->getCell('D'.substr($codata, 1))->getValue();
                $n = $sheet->getCell('E'.substr($codata, 1))->getValue();
                $picname = "d:/Inbox/pic/".$p.'n'.$n.'.png';

                $zitouArr[$i] = $picname;
            
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );
                $imageContents = ob_get_contents();

                // 把文件保存到本地
                file_put_contents($picname, $imageContents); 
                ob_end_clean();
                $i++;
        }
 
        foreach ($zitouArr as $key => $value) {
            echo $key. $value.'<br>';
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
