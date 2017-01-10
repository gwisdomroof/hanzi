<?php

namespace frontend\controllers;

use Yii;
use common\models\HanziHyyt;
use common\models\HanziUserTask;
use common\models\search\HanziUserTaskSearch;
use common\models\HanziTask;
use common\models\search\HanziHyytSearch;
use common\models\WorkPackage;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HanziHyytController implements the CRUD actions for HanziHyyt model.
 */
class TransController extends Controller
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
     * Lists all HanziUserTask models.
     * @return mixed
     */
    public function actionPkgid()
    {
        $models = HanziUserTask::find()->limit(20000000)->orderBy(['userid' => SORT_ASC, 'task_type' => SORT_ASC, 'task_seq' => SORT_ASC, 'created_at' => SORT_ASC])->all();

        $sqls = [];
        $sqls[] = 'insert into work_package';
        $sqls[] = '(userid, type, stage, volume, daily_schedule, progress, status, expected_date, created_at, updated_at) values ';

        $curuserid = $curtype = $curstage = 0;
        $preuserid = $pretype = $prestage = 0;
        $num = $daily_schedule = 0;
        $c_t = $u_t = $e_t = 0;
        $status = 0;

        $index = 0;
        $count = count($models);

        foreach ($models as $model) {
            $index++;
            $curuserid = $model->userid;
            $curtype = $model->task_type;
            $curstage = $model->task_seq;

            if ($curuserid != $preuserid || $curtype != $pretype || $curstage != $prestage || $index == $count || $num == 1000) {
                // 三者任何一个不相等，都需要重新建一个任务包
                $volume = $num;
                if ($volume % 10 != 0)
                    $volume = (int)($volume / 10 + 1) * 10;
                $days = (int)(($u_t - $c_t) / 86400); // 一天的时间为86400
                $days = $days + 1;
                $daily_schedule = (int)($num / $days);
                if ($daily_schedule < 1)
                    $daily_schedule = 5;
                else if ($daily_schedule % 5 != 0)
                    $daily_schedule = (int)($daily_schedule / 5 + 1) * 5;

                if ($volume == $num) {
                    $status = 1;
                    $e_t = $u_t;
                } else {
                    $status = 0;
                    $schedule_days = ($volume-$num)/$daily_schedule;
                    $e_t = $c_t + 86400*$schedule_days;
                }
                $sqls[] = "({$preuserid}, {$pretype}, {$prestage}, {$volume}, {$daily_schedule}, {$num}, {$status}, {$e_t}, {$c_t}, {$u_t}),";

                // 初始化后面的计算
                $num = 1;
                $c_t = $model->created_at;
                $u_t = $model->updated_at;

            } else {
                $num++;
                $u_t = $model->updated_at > $u_t ? $model->updated_at : $u_t;
            }

            $preuserid = $curuserid;
            $pretype = $curtype;
            $prestage = $curstage;

        }

        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\add-left-pkg.txt', $contents);

        echo "{$index} records.<br/>success!";
        die;
    }

    /**
     * 更新任务包.
     * @param page 页面
     * @param seq 阶段
     * @return mixed
     */
    public function actionUpdateWorkPackage()
    {
        $models = WorkPackage::find()->orderBy('id')->where('progress > volume')->all();
        $sqls = [];
        $index = 1;
        foreach ($models as $model) {
            $picnameArr = explode('n', $model->picture);
            if (count($picnameArr) < 2)
                continue;
            $page = str_pad($picnameArr[0], 4, "0", STR_PAD_LEFT);
            $num = str_pad($picnameArr[1], 2, "0", STR_PAD_LEFT);
            $pic_id = "zh{$page}{$num}";

            $sqls[] = "update variants_input set hanzi_pic_id_draft = '{$pic_id}' where id = $model->id;";

        }
        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\set-hyyt-pic-id.txt', $contents);

        echo "success!";
        die;

    }


    /**
     * 修改汉语大字典录入图片字的名称.
     * @param page 页面
     * @param seq 阶段
     * @return mixed
     */
    public function actionHyytRename()
    {
        $models = HanziHyyt::find()->orderBy('id')->where("picture != ''")->all();
        $sqls = [];
        $index = 1;
        foreach ($models as $model) {
            $picnameArr = explode('n', $model->picture);
            if (count($picnameArr) < 2)
                continue;
            $page = str_pad($picnameArr[0], 4, "0", STR_PAD_LEFT);
            $num = str_pad($picnameArr[1], 2, "0", STR_PAD_LEFT);
            $pic_id = "zh{$page}{$num}";

            $sqls[] = "update variants_input set hanzi_pic_id_draft = '{$pic_id}' where id = $model->id;";

        }
        $contents = implode("\r\n", $sqls);
        file_put_contents('d:\Inbox\set-hyyt-pic-id.txt', $contents);

        echo "success!";
        die;

    }


}
