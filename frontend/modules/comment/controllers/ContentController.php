<?php

namespace app\modules\comment\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\comment\models\Comment;
use app\modules\comment\models\CommentHash;

/**
 * Default controller for the `comment` module
 */
class ContentController extends Controller {
    public function renderJson($data) {
        $response=Yii::$app->response;  
        $response->format=Response::FORMAT_JSON;

        $response->data = $data;
    }

    public function actionHash() {
        $url = $_GET['url'];
        $model = new CommentHash();
        $hash = $model->getId($url);
        $this->renderJson($hash);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionGet() {
        $model = new Comment();
        $dataProvider = $model->search($_GET['hash']);
        $_list = $dataProvider->getModels();

        
        $children = [];
        $list     = [];
        $all      = [];
        foreach ($_list as $key => $row) {
            if (!isset($row['parent_id']) || $row['parent_id'] == 0) {
                $list[] = $row->attributes;
            }
            $all[] = $row->attributes;
        }
        foreach ($list as $key => $row) {
            $list[$key]['children'] = $this->getChildren($row['id'], $all);
        }

        $this->renderJson($list);
    }

    private function getChildren($parent_id, $all) {
        $children = [];
        foreach ($all as $key => $row) {
            if ($row['parent_id'] == $parent_id) {
                $children[] = $row;
            }
        }
        foreach ($children as $key => $child) {
            $_children = $this->getChildren($child['id'], $all);
            $children[$key]['children'] = $_children;
        }
        return $children;
    }

    public function actionAdd() {
        $model = new Comment();
        $model->hash       = $_POST['hash'];
        $model->content    = $_POST['content'];
        $model->parent_id  = 0;
        $model->username   = Yii::$app->user->identity['username'];
        $model->created_at = time();
        $model->updated_at = time();
        $ret = $model->save();

        $this->renderJson(['ret' => 1]);
    }

    public function actionReply() {
        $model = new Comment();
        $model->hash       = $_POST['hash'];
        $model->content    = $_POST['content'];
        $model->parent_id  = $_POST['parent_id'];
        $model->level      = 2;
        $model->username   = Yii::$app->user->identity['username'];
        $model->created_at = time();
        $model->updated_at = time();
        $ret = $model->save();

        $this->renderJson(['ret' => 1]);
    }
}
