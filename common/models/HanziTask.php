<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use common\models\MemberRelation;
use common\models\HanziHyyt;
use yii\db\Query;

/**
 * This is the model class for table "{{%hanzi_task}}".
 *
 * @property string $id
 * @property integer $leader_id
 * @property integer $user_id
 * @property integer $page
 * @property integer $seq
 * @property integer $startid
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $task_type
 */
class HanziTask extends \yii\db\ActiveRecord
{
    const TYPE_SPLIT = 1;
    const TYPE_INPUT = 2;
    const TYPE_COLLATE = 3;
    const TYPE_DOWNLOAD = 4;
    const TYPE_DEDUP = 5;
    const TYPE_GAOLI_SPLIT = 6;

    const STATUS_ASSIGNMENT = 0;
    const STATUS_ONGOING = 1;
    const STATUS_CANCEL = 2;
    const STATUS_COMPLETE = 3;
    const STATUS_CONTINUE = 4;  // 待继续，可以给其他人接续做

    const SEQ_FIRST = 1;
    const SEQ_SECOND = 2;
    const SEQ_THIRD = 3;

    public $cnt;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%hanzi_task}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'leader_id', 'page', 'seq', 'start_id', 'end_id', 'status', 'created_at', 'updated_at', 'task_type', 'cnt'], 'integer'],
            [['page'], 'integer', 'max' => 10000],
            [['leader.username', 'member.username', 'user.username', 'created_at', 'updated_at'], 'safe'],
            [['remark'], 'string', 'max' => 128],
            ['page', function ($attribute, $params) {
                if ($this->status == self::STATUS_CANCEL || $this->status == self::STATUS_CONTINUE) {
                    return true;
                }
                // 1. create, current page is not allowed duplicated except the record is canceled
                $query = HanziTask::find()
                    ->where(['page' => $this->page, 'task_type' => $this->task_type, 'seq' => $this->seq])
                    ->andWhere(['!=', 'status', self::STATUS_CANCEL])
                    ->andWhere(['!=', 'status', self::STATUS_CONTINUE]);
                // 2. update, except current id
                if (!empty($this->id)) {
                    $query->andFilterWhere(['!=', 'id', $this->id]);
                }
                if ($query->exists()) {
                    $this->addError('page', '该页面已分配。');
                }
            }],
        ];
    }

    /**
     * 获取系统组长
     * @return [type] [description]
     */
    public static function getSystemLeader()
    {
        return User::getUsersByRole("系统组长")[0];
    }

    /**
     * 根据任务类型，获取当前可分配的页码
     * @return [type] [description]
     */
    public static function getIdlePages($type = self::TYPE_SPLIT, $count = 50)
    {
        // 待处理的记录总数
        $maxPageNumber = $seq = null;
        if ($type == self::TYPE_SPLIT) {
            $countToSplit = HanziSplit::find()->where(['duplicate' => 0, 'source' => HanziSet::SOURCE_TAIWAN])->count();
            $maxPageNumber = (int)($countToSplit / Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false)) + 1;
            $seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
        } elseif ($type == self::TYPE_GAOLI_SPLIT) {
            $countToSplit = HanziSplit::find()->where(['duplicate' => 0, 'source' => HanziSet::SOURCE_GAOLI])->count();
            $maxPageNumber = (int)($countToSplit / Yii::$app->get('keyStorage')->get('frontend.task-per-page', null, false)) + 1;
            $seq = Yii::$app->get('keyStorage')->get('frontend.current-gaoli-split-stage', null, false);
        } else {
            $maxPageNumber = 5127; // 汉语大字典最大5127页
            $seq = Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false);
        }

        # 第二阶段为回查
        # 高丽异体字回查，取消公共页面的设置
//        if ($seq == self::SEQ_SECOND) {
//            return self::getSecondStageSplitIdlePages();
//        }

        // 已申请的任务页码
        $usedPages = HanziTask::find()->select('page')
            ->where(['task_type' => $type, 'seq' => $seq])
            ->andWhere(['!=', 'status', self::STATUS_CANCEL])
            ->andWhere(['!=', 'status', self::STATUS_CONTINUE])
            ->orderBy('id')
            ->asArray()
            ->all();

        $usedPagesArr = [];
        foreach ($usedPages as $page) {
            if (!empty($page['page']))
                $usedPagesArr[] = $page['page'];
        }

        $idlePagesArr = [];
        $gaoliSplitOffset = 1021;   # 高丽异体字第一页为1022
        # 异体字输入时的空白页面
        $inputEmptyPages = json_decode(Yii::$app->get('keyStorage')->get('frontend.input-empty-pages', null, false));
        for ($i = 1; $i <= $maxPageNumber; $i++) {
            # 如果是异体字输入，则需要去掉空白页面
            if ($type == self::TYPE_INPUT && in_array($i . '', $inputEmptyPages)) {
                continue;
            }
            if (count($idlePagesArr) >= $count) {
                break;
            }
            $j = $type == self::TYPE_GAOLI_SPLIT ? $gaoliSplitOffset + $i : $i;
            if (!in_array($j, $usedPagesArr)) {
                $idlePagesArr[$j] = $j;
            }
        }

        return $idlePagesArr;
    }

    /**
     * 获取二次拆分时的页面
     * @return [type] [description]
     */
    public static function getSecondStageSplitIdlePages()
    {
        # 获取用户初次拆分完成而尚未二次拆分的页面
        $userId = Yii::$app->user->id;
        $taskType = HanziTask::TYPE_SPLIT;
        $sql = "SELECT DISTINCT page FROM hanzi_task 
            WHERE user_id  = {$userId} AND task_type = {$taskType} AND status = 3 AND seq = 1 
            AND page NOT IN ( SELECT page from hanzi_task WHERE task_type = {$taskType} AND seq = 2 )
            ORDER BY page";
        $myPages = HanziTask::findBySql($sql)->asArray()->one();
        if (!empty($myPages)) {
            $page = $myPages['page'];
            return [$page => $page];
        }

        # 获取公共页面
        $sql = "SELECT DISTINCT page FROM common_page
            WHERE task_type = {$taskType} AND seq = 2
            AND page NOT IN ( SELECT page from hanzi_task WHERE task_type = {$taskType} AND seq = 2 )
            ORDER BY page";
        $commonPages = HanziTask::findBySql($sql)->asArray()->one();
        if (!empty($commonPages)) {
            $page = $commonPages['page'];
            return [$page => $page];
        }

        return false;

    }

    /**
     * 检查用户是否有未完成的任务，如果有，则将状态设置为continue，同时设置end_id为
     * @return [type] [description]
     */
    public static function getFinishedTasksByOthers($userid, $page, $type)
    {
        // 获取page对应的id范围
        $idOfPage = HanziHyyt::find()->select('id')->where(['page' => $page])->orderBy('id')->asArray()->all();
        $minId = reset($idOfPage)['id'];
        $maxId = end($idOfPage)['id'];

        // 获取已完成的id
        $finishedIds = HanziUserTask::find()->select('userid, taskid')->where([
            'task_seq' => Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false),
            'task_type' => $type,])
            ->andWhere(['>=', 'taskid', $minId])
            ->andWhere(['<=', 'taskid', $maxId])
            ->asArray()
            ->all();

        $sortIds = [];
        foreach ($finishedIds as $id) {
            if ($id['userid'] == $userid) {
                $sortIds['mine'][] = (int)$id['taskid'];
            } else {
                $sortIds['others'][] = (int)$id['taskid'];
            }
        }

        return $sortIds;
    }

    /**
     * 获取从某日开始已完成工作量
     * @param int $userId 用户名
     * @param int $taskType 任务类型
     * @param int $start 开始日期的时间，timestamp的整数
     * @return mixed
     */
    public static function getFinishedWorkCountFrom($userId, $taskType = self::TYPE_DEDUP, $start)
    {
        return HanziTask::find()
            ->where(['user_id' => $userId, 'task_type' => $taskType, 'status' => self::STATUS_COMPLETE])
            ->andWhere(['>=', 'created_at', $start])
            ->andWhere(['<=', 'created_at', time()])
            ->count();
    }

    /**
     * 获取今日已完成工作量
     * @param string $packageStart 当前工作包的创建时间
     * @return mixed
     */
    public static function getFinishedWorkCountToday($userId, $taskType = self::TYPE_DEDUP, $packageStart = 0)
    {
        $begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        if (!empty($packageStart) && $begin < $packageStart) {
            $begin = $packageStart;
        }
        $end = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        return HanziTask::find()
            ->where(['user_id' => $userId, 'task_type' => $taskType, 'status' => self::STATUS_COMPLETE])
            ->andWhere(['>=', 'created_at', $begin])
            ->andWhere(['<=', 'created_at', $end])
            ->count();
    }

    /**
     * 检查用户是否有未完成的任务，如果有，则将状态设置为continue，同时设置end_id为
     * @return [type] [description]
     */
    public static function setUnfinishedHyytPage($userid)
    {
        $model = HanziTask::find()->where(['user_id' => $userid])
            ->andWhere(['<=', 'status', self::STATUS_ONGOING])
            ->orderBy('page')
            ->one();
        if (!empty($model)) {
            $model->status = self::STATUS_CONTINUE;
            $model->update();
        }
    }

    /**
     * 检查用户是否有未完成的任务，如果有，则将状态设置为continue，同时设置end_id为
     * @return [type] [description]
     */
    public static function checkFinished($taskid, $pagenum)
    {
        // 待完成工作量
        $countTodo = HanziHyyt::find()->where(['page' => $pagenum])->count();

        // 已完成工作量
        $seq = Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false);
        // 获取page对应的id范围
        $idOfPage = HanziHyyt::find()->select('id')->where(['page' => $pagenum])->orderBy('id')->asArray()->all();
        $minId = reset($idOfPage)['id'];
        $maxId = end($idOfPage)['id'];

        // 获取已完成数量
        $finished = HanziUserTask::find()->select('taskid')->where([
            'task_seq' => Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false),
            'task_type' => self::TYPE_INPUT])
            ->andWhere(['>=', 'taskid', $minId])
            ->andWhere(['<=', 'taskid', $maxId])
            ->count();

        if ($finished >= $countTodo) {
            $command = Yii::$app->db->createCommand("update hanzi_task set status=" . self::STATUS_COMPLETE . " where id = {$taskid}");
            $command->execute();
            return true;
        }
        return false;
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getMember()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getLeader()
    {
        return $this->hasOne(User::className(), ['id' => 'leader_id']);
    }

    /**
     * [getCustomer description]
     * @return [type] [description]
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'task_type' => Yii::t('app', '任务类型'),
            'leader_id' => Yii::t('app', '组长'),
            'user_id' => Yii::t('app', '组员'),
            'leader.username' => Yii::t('app', '组长'),
            'member.username' => Yii::t('app', '组员'),
            'user.username' => Yii::t('app', '用户名'),
            'page' => Yii::t('app', '页码'),
            'seq' => Yii::t('app', '阶段'),
            'start_id' => Yii::t('app', '起始ID'),
            'end_id' => Yii::t('app', '结束ID'),
            'status' => Yii::t('app', '状态'),
            'remark' => Yii::t('app', '备注'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }


    public function attributes()
    {
        // 添加关联字段到可搜索特性
        return array_merge(parent::attributes(), ['member.username', 'leader.username', 'user.username']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // 如果是汉字拆分任务，则须保存id范围
            if ($this->task_type == self::TYPE_SPLIT || $this->task_type == self::TYPE_GAOLI_SPLIT) {
                $idRange = HanziSplit::getIdRangeByPage($this->page);
                $this->start_id = $idRange['minId'];
                $this->end_id = $idRange['maxId'];
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 寻找页面池中page值最小，状态为“初分配”“已完成”的页面
     * @param string $id
     * @return mixed
     */
    public static function getNewPage($userId, $taskType = self::TYPE_SPLIT)
    {
        $model = new HanziTask();
        $model->leader_id = 3;  // 生产环境中，id为3，指的是贤二
        $model->user_id = Yii::$app->user->id;
        $model->task_type = $taskType;
        $model->status = self::STATUS_ASSIGNMENT;
        if ($taskType == HanziTask::TYPE_DEDUP) {
            $model->page = GltwDedup::getNewPage();
        } else {
            $idlePages = self::getIdlePages($taskType, 1);
            if (empty($idlePages)) {
                return false;
            }
            $model->page = current($idlePages);
        }
        if ($taskType == HanziTask::TYPE_SPLIT) {
            $model->seq = Yii::$app->get('keyStorage')->get('frontend.current-split-stage', null, false);
        } elseif ($taskType == HanziTask::TYPE_INPUT) {
            $model->seq = Yii::$app->get('keyStorage')->get('frontend.current-input-stage', null, false);
        } elseif ($taskType == HanziTask::TYPE_DEDUP) {
            $model->seq = Yii::$app->get('keyStorage')->get('frontend.current-dedup-stage', null, false);
        } elseif ($taskType == HanziTask::TYPE_GAOLI_SPLIT) {
            $model->seq = Yii::$app->get('keyStorage')->get('frontend.current-gaoli-split-stage', null, false);
        }

        if (!$model->validate() || !$model->save()) {
            var_dump($model->getErrors());
            die;
        }

        return $model;
    }

    /**
     * 更新任务状态
     * @param string $id
     * @return mixed
     */
    public static function updateStatus($taskId, $status)
    {
        $task = HanziTask::findOne($taskId);
        $task->status = $status;
        $task->update();
    }

    /**
     * 寻找页面池中page值最小，状态为“初分配”“进行中”的页面
     * @param string $id
     * @return mixed
     */
    public static function getUnfinishedMinPage($userId, $taskType = self::TYPE_SPLIT)
    {
        return HanziTask::find()
            ->where(['user_id' => $userId, 'task_type' => $taskType])
            ->andWhere(['<=', 'status', self::STATUS_ONGOING])
            ->orderBy(['seq' => SORT_ASC, 'page' => SORT_ASC])
            ->one();
    }

    /**
     * 根据任务ID获取任务对应的阶段
     * @param string $id
     * @return mixed
     */
    public static function getSeq($userId, $id, $taskType = 1)
    {
        $model = HanziTask::find()->where(['user_id' => $userId, 'task_type' => $taskType])
            ->andWhere(['<=', 'start_id', $id])
            ->andWhere(['>=', 'end_id', $id])
            ->orderBy('seq')
            ->one();
        return empty($model) ? null : $model->seq;
    }

    /**
     * 根据任务ID获取任务对应的阶段
     * @param string $id
     * @return mixed
     */
    public static function getTaskByPage($userId, $page, $taskType = 1)
    {
        return HanziTask::find()->where(['user_id' => $userId, 'task_type' => $taskType, 'page' => $page])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }

    /**
     * 如果有未完成的任务，则不能继续申请
     * @param string $id
     * @return mixed
     */
    public static function checkApplyPermission($userId, $taskType)
    {
        return HanziTask::find()->where(['user_id' => $userId, 'task_type' => $taskType])
            ->andWhere(['<=', 'status', self::STATUS_ONGOING])
            ->exists();
    }

    /**
     * 拆字任务，检查是否有对该字的拆分权限
     * @param string $id
     * @return mixed
     */
    public static function checkIdPermission($userId, $id, $seq, $taskType = self::TYPE_SPLIT)
    {
        return HanziTask::find()->where(['user_id' => $userId, 'seq' => $seq, 'task_type' => $taskType])
            ->andWhere(['<=', 'start_id', $id])
            ->andWhere(['>=', 'end_id', $id])
            ->exists();
    }

    /**
     * 拆字任务，检查是否有对该页的拆分权限
     * @param string $id
     * @return mixed
     */
    public static function checkPagePermission($userId, $page, $seq = 0, $taskType = self::TYPE_SPLIT)
    {
        $query = HanziTask::find()->where(['user_id' => $userId, 'page' => $page, 'task_type' => $taskType]);
        if ($seq !== 0) {
            $query->andWhere(['seq' => $seq]);
        }
        return $query->exists();
    }

    /**
     * 阶段
     * Returns user statuses list
     * @return array|mixed
     */
    public static function seqs()
    {
        return [
            self::SEQ_FIRST => Yii::t('common', '初次'),
            self::SEQ_SECOND => Yii::t('common', '二次'),
            self::SEQ_THIRD => Yii::t('common', '审查'),
        ];
    }

    /**
     * 状态类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_ASSIGNMENT => Yii::t('common', '初分配'),
            self::STATUS_ONGOING => Yii::t('common', '进行中'),
            self::STATUS_CANCEL => Yii::t('common', '已取消'),
            self::STATUS_COMPLETE => Yii::t('common', '已完成'),
            self::STATUS_CONTINUE => Yii::t('common', '已移交')
        ];
    }

    /**
     * 任务类型
     * Returns user statuses list
     * @return array|mixed
     */
    public static function types()
    {
        return [
            self::TYPE_SPLIT => Yii::t('common', '台湾异体字拆字'),
            self::TYPE_GAOLI_SPLIT => Yii::t('common', '高丽异体字拆字'),
            self::TYPE_DEDUP => Yii::t('common', '高丽台湾异体字去重'),
            self::TYPE_INPUT => Yii::t('common', '异体字录入'),
            self::TYPE_COLLATE => Yii::t('common', '图书校对'),
            self::TYPE_DOWNLOAD => Yii::t('common', '论文下载')
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function isLeader($id)
    {
        $roles = Yii::$app->authManager->getRolesByUser($id);
        return array_key_exists('组长', $roles);
    }

    /**
     * 是否为校勘专员
     * @return array|mixed
     */
    public static function isCollater($id)
    {
        $roles = Yii::$app->authManager->getRolesByUser($id);
        return array_key_exists('校勘专员', $roles);
    }

    /**
     * @param get members of leader
     * @return mixed
     */
    public function members($userId = null)
    {
        $items[Yii::$app->user->id] = Yii::$app->user->identity->username;
        $users = MemberRelation::find()->with('member')->where(['leader_id' => Yii::$app->user->id])->all();
        foreach ($users as $user) {
            $items[$user->member_id] = $user->member->username;
        }
        if (!empty($userId)) {
            $user = User::findOne(['id' => $userId]);
            $items[$userId] = !empty($user) ? $user->username : '';
        }
        return $items;
    }

}
