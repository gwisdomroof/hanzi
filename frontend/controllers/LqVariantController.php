<?php

namespace frontend\controllers;

use Yii;
use common\models\LqVariant;
use common\models\search\LqVariantSearch;
use common\models\HanziSet;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LqVariantController implements the CRUD actions for LqVariant model.
 */
class LqVariantController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 重置图片路径.
     * @return mixed
     */
    private function actionResetImage()
    {
        $basePath = 'yitizi';
        $paths = ['a', 'b', 'c', 'n'];
        $files = [];
        foreach ($paths as $path) {
            $dir = "{$basePath}/yiti{$path}/s{$path}";
            $handle = opendir($dir);
            if ($handle) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        $filePath = $dir . "/" . $file;
                        if (is_file($filePath)) {  # 如果是文件，则进行查找替换
                            $content = file_get_contents($filePath);
                            $search = 'http://yitizi.guoxuedashi.com';
                            $replace = 'http://dict.variants.moe.edu.tw';
                            $content = str_replace($search, $replace, $content);
                            file_put_contents($filePath, $content);
                            $files[] = $filePath;
                        }
                    }
                }   //  end while
                closedir($handle);
            }
        }

        file_put_contents('\home\xiandu\reset-image-files.txt', implode("\r\n", $files));
        echo 'success!';
        die;
    }

    /**
     * Import LqVariants from lq-variant-check.
     * @return mixed
     */
    public function actionImport()
    {
        $lastImportTime = Yii::$app->get('keyStorage')->get('frontend.last-lq-variant-import-time', null, false);
        $models = \common\models\LqVariantCheck::find()->where(['bconfirm' => 1])->andwhere(['>', 'updated_at', $lastImportTime])->orderBy('id')->all();

        $curImportTime = $lastImportTime;
        $sqls = [];
        foreach ($models as $model) {
            $source = LqVariant::SOURCE_TH;
            if (stripos($model->pic_name, "TW-") !== false) {
                $source = LqVariant::SOURCE_TW;
            } elseif (stripos($model->pic_name, "GL-") !== false) {
                $source = LqVariant::SOURCE_GL;
            } elseif (stripos($model->pic_name, "SZ-") !== false) {
                $source = LqVariant::SOURCE_SZ;
            }
            $word = mb_strlen($model->variant_code) == 1 ? $model->variant_code : null;
            $picName = mb_strlen($model->variant_code) == 1 ? null : $model->variant_code;
            $oriPicName = $model->origin_standard_word_code . $model->pic_name;
            $type = empty($model->nor_var_type) ? 'null' : $model->nor_var_type;
            $time = time();
            $sqls[] = "INSERT INTO lq_variant(source, ori_pic_name, word, pic_name, belong_standard_word_code, nor_var_type, created_at, updated_at) " .
                "VALUES ({$source}, '{$oriPicName}', '{$word}', '{$picName}', '{$model->belong_standard_word_code}', {$type}, {$time}, {$time});";

            if ($curImportTime < $model->updated_at) {
                $curImportTime = $model->updated_at;
            }
        }

        $contents = implode("\r\n", $sqls);
//        file_put_contents('d:\Inbox\import-lq-variant-'.date('Y-m-d-H-i-s', time()).'.txt', $contents);
        file_put_contents('/home/xiandu/import-lq-variant-' . date('Y-m-d-H-i-s', time()) . '.txt', $contents);

        Yii::$app->get('keyStorage')->set('frontend.last-lq-variant-import-time', $curImportTime);
        echo 'success!';
        die;
    }

    /**
     * Lists all LqVariant models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new LqVariantSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LqVariant model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LqVariant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LqVariant();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LqVariant model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing LqVariant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LqVariant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LqVariant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LqVariant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
