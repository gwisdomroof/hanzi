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
            ['label' => Yii::t('frontend', '部件笔画检字法'), 'url' => ['/hanzi-set/search'],],
            ['label' => Yii::t('frontend', '我的任务'), 'url' => ['/hanzi-task/index'],],
            ['label' => Yii::t('frontend', '任务管理'), 'url' => ['/hanzi-task/admin'],],
            ['label' => Yii::t('frontend', '拆字'), 'url' => ['/hanzi-split/index'],],
            ['label' => Yii::t('frontend', '部件集'), 'url' => ['/hanzi-split/component'],],
            ['label' => Yii::t('frontend', '帮助'), 'url' => ['/article/split-intro'],],

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
        <p class="pull-right"><?php echo Yii::powered() ?></p>
    </div>
</footer>
<?php $this->endContent() ?>