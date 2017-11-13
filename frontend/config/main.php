<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    //设置语言
    'language'=>'zh-CN',
    //设置布局文件
    'layout'=>false,
    //默认路由
    'defaultRoute'=>'member/index',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'class'=>'yii\web\User',
            //指定实现认证接口的类
            'identityClass' => 'frontend\models\Member',
            'enableAutoLogin' => true,
            'on beforeLogin' => function($event) {
                $user = $event->identity;
                $user->last_login_time = time();
                $user->last_login_ip = Yii::$app->request->userIP;
                $user->save(0);
            },
            'on afterLogin' => function($event) {
                $user = $event->identity;
                $user->last_login_time = time();
                $user->last_login_ip = Yii::$app->request->userIP;
                $user->save();
            },
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['member/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            /*   'suffix'=>'.html',*/
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
