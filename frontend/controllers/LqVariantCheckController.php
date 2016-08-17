<?php

namespace frontend\controllers;

use Yii;
use common\models\LqVariantCheck;
use common\models\LqVariantCheckSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LqVariantCheckController implements the CRUD actions for LqVariantCheck model.
 */
class LqVariantCheckController extends Controller
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
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LqVariantCheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all LqVariantCheck models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new LqVariantCheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

   /**
     * @param string $id
     * @return mixed
     */
    public function actionModify($id)
    {
        if (!Yii::$app->request->isAjax) {
            return false;
        }

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return '{"status":"success", "id": ' . $id. '}';
        } else {
            return '{"status":"error", "id": ' . $id. '}';
        }
    }


    /**
     * Displays a single LqVariantCheck model.
     * @param string $id
     * @return mixed
     */
    public function actionScan111()
    {
        $base = "e:/Code/hanzi/frontend/web/img/FontImage";
        // $normals = json_decode(Yii::$app->get('keyStorage')->get('frontend.yitizi', null, false));
        $files = [];
        $falseFiles = [];
        $normals = [];
        $handle = opendir($base);
        if($handle) {
            # 第一层
            while(false !== ($dir = readdir($handle))) {
                if ($dir != '.' && $dir != '..') {
                    $dirname = $base . "/"  . $dir;
                    if (is_dir($dirname) ) {
                        # 第二层
                        $handle2 = opendir($dirname);
                        if ($handle2) {
                            while(false !== ($file = readdir($handle2))) {
                                if ($file != '.' && $file != '..') {
                                    $filename = $dirname . "/"  . $file;
                                    if (is_file($filename) ) {
                                        $suffix = substr(strrchr($file, '.'), 1);
                                        if ($suffix == "png" || $suffix == "jpg")
                                            $files[$dir][] = $file;
                                    }
                                }
                            }
                            closedir($handle2);
                        }
                    } else {
                        $falseFiles[] = $dir;
                    }
                }
            }   //  end while
            closedir($handle);
        }

        $sqls = [];
        foreach ($files as $normal => $pics) {
            foreach ($pics as $pic) {
                $source = 1;
                $sourceStr = explode('-', $pic)[0];
                if ($sourceStr == 'GL') {
                   $source = 2; 
                }
                # insert into lq_variant_check (source, pic_name, belong_standard_word_code1, belong_standard_word_code2) VALUES ('', '');
                $sqls[] = "insert into lq_variant_check (source, pic_name, belong_standard_word_code1, belong_standard_word_code2) VALUES (".$source.", '".$pic."', '".$normal."', '".$normal."')";
            }
        }

        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\lq-variant-check.txt', $contents);

        echo "success!";
        die;

    }

    /**
     * Creates a new LqVariantCheck model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LqVariantCheck();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LqVariantCheck model.
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
     * Deletes an existing LqVariantCheck model.
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
     * Finds the LqVariantCheck model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LqVariantCheck the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LqVariantCheck::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
