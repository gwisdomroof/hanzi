<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@frontend/views/layouts/_clear.php')
?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]); ?>
    <?php echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            // ['label' => Yii::t('frontend', 'Home'), 'url' => ['/site/index']],
            // ['label' => Yii::t('frontend', 'About'), 'url' => ['/page/view', 'slug'=>'about']],
            // ['label' => Yii::t('frontend', 'Articles'), 'url' => ['/article/index']],
            ['label' => Yii::t('frontend', '部件笔画检字法'), 'url' => ['/hanzi-set/search'],'visible'=>!Yii::$app->user->isGuest,],
            ['label' => Yii::t('frontend', '异体字检索'), 'url' => ['/hanzi-dict/search'],'visible'=>!Yii::$app->user->isGuest,],
            ['label' => Yii::t('frontend', '积分排名'), 'url' => ['/user-task/order'],'visible'=>!Yii::$app->user->isGuest,],
            [
                'label' => Yii::t('frontend', '校勘工作坊'),
                'visible'=>\common\models\HanziTask::isCollater(Yii::$app->user->id),
                'items'=>[
                    [
                        'label' => Yii::t('frontend', '异体字判取'),
                        'url' => ['/lq-variant-check/index']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字审查'),
                        'url' => ['/lq-variant-check/pages']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字提交'),
                        'url' => ['/lq-variant-check/create']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字检索'),
                        'url' => ['/lq-variant-check/list']
                    ]
                ]
            ],
            [
                'label' => Yii::t('frontend', '任务管理'),
                'visible'=>\common\models\HanziTask::isLeader(Yii::$app->user->id),
                'items'=>[
                    [
                        'label' => Yii::t('frontend', '异体字拆字'),
                        'url' => ['/hanzi-task/admin?type=1']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字识别'),
                        'url' => ['/hanzi-task/admin?type=2']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字去重'),
                        'url' => ['/hanzi-task/admin?type=5']
                    ],
                    [
                        'label' => Yii::t('frontend', '图书校对'),
                        'url' => ['/user-task/admin?type=3']
                    ],
                    [
                        'label' => Yii::t('frontend', '论文下载'),
                        'url' => ['/user-task/admin?type=4']
                    ],
                ]
            ],
            [
                'label' => Yii::t('frontend', '我的任务'),
                'visible'=>!Yii::$app->user->isGuest,
                'items'=>[
                    [
                        'label' => Yii::t('frontend', '任 务'),
                        'url' => ['/work-package/index']
                    ],
                    [
                        'label' => Yii::t('frontend', '打 卡'),
                        'url' => ['/work-clock/index']
                    ],
                    [
                        'label' => Yii::t('frontend', '积 分'),
                        'url' => ['/score-exchange/index']
                    ]
                ]
            ],
            [
                'label' => Yii::t('frontend', '管理员'),
                'visible' => \common\models\User::isFrontManager(Yii::$app->user->id),
                'items'=>[
                    [
                        'label' => Yii::t('frontend', '任务包管理'),
                        'url' => ['/work-package/admin']
                    ],
                    [
                        'label' => Yii::t('frontend', '打卡管理'),
                        'url' => ['/work-clock/admin']
                    ],
                    [
                        'label' => Yii::t('frontend', '积分兑换'),
                        'url' => ['/score-exchange/admin']
                    ],
                    [
                        'label' => Yii::t('frontend', '用户信息'),
                        'url' => ['/user-admin/index']
                    ],
                ]
            ],
            [
                'label' => Yii::t('frontend', '帮助'),
                'visible'=>!Yii::$app->user->isGuest,
                'items'=>[
                    [
                        'label' => Yii::t('frontend', '如何开始工作'),
                        'url' => ['/article/work-intro']
                    ],
                    [
                        'label' => Yii::t('frontend', '部件笔画检字法'),
                        'url' => ['/article/search-intro']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字拆字实例'),
                        'url' => ['/article/split-example']
                    ],
                    [
                        'label' => Yii::t('frontend', '汉字部件表简介'),
                        'url' => ['/article/component-intro']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字录入简介'),
                        'url' => ['/article/variant-help']
                    ],
                    [
                        'label' => Yii::t('frontend', '异体字去重简介'),
                        'url' => ['/article/dedup-help']
                    ]
                ]
            ],

            // ['label' => Yii::t('frontend', 'Contact'), 'url' => ['/site/contact']],
            // ['label' => Yii::t('frontend', 'Signup'), 'url' => ['/user/sign-in/signup'], 'visible'=>Yii::$app->user->isGuest],
            ['label' => Yii::t('frontend', 'Login'), 'url' => ['/user/sign-in/login'], 'visible'=>Yii::$app->user->isGuest],
            [
                'label' => Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->getPublicIdentity(),
                'visible'=>!Yii::$app->user->isGuest,
                'items'=>[
                    [
                        'label' => Yii::t('frontend', 'Settings'),
                        'url' => ['/user/default/index']
                    ],
                    [
                        'label' => Yii::t('frontend', 'Backend'),
                        'url' => Yii::getAlias('@backendUrl'),
                        'visible'=>Yii::$app->user->can('manager')
                    ],
                    [
                        'label' => Yii::t('frontend', 'Logout'),
                        'url' => ['/user/sign-in/logout'],
                        'linkOptions' => ['data-method' => 'post']
                    ]
                ]
            ],
            // [
            //     'label'=>Yii::t('frontend', 'Language'),
            //     'items'=>array_map(function ($code) {
            //         return [
            //             'label' => Yii::$app->params['availableLocales'][$code],
            //             'url' => ['/site/set-locale', 'locale'=>$code],
            //             'active' => Yii::$app->language === $code
            //         ];
            //     }, array_keys(Yii::$app->params['availableLocales']))
            // ]
        ]
    ]); ?>
    <?php NavBar::end(); ?>

    <?php echo $content ?>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Lqs <?php echo date('Y') ?></p>
        <!-- <p class="pull-right"><?php echo Yii::powered() ?></p>  -->
    </div>
</footer>
<?php $this->endContent() ?>