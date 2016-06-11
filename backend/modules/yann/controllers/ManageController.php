<?php

namespace app\modules\yann\controllers;

use Yii;
use yii\web\Controller;
use app\modules\yann\models\Comment;

/**
 * Default controller for the `comment` module
 */
class ManageController extends Controller {
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        //$list = Comment::findAll(1);
        $model = new Comment();
        $dataProvider = $model->search(Yii::$app->request->queryParams);
        // $model = new Comment();
        // $model->hash = '123123123123123';
        // $model->content = '123123123123123';
        // $model->created_at = time();
        // $model->updated_at = time();
        // $ret = $model->save();
        // var_export($ret);die;
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}
