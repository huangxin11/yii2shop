<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    //设置语言
    'language'=>'zh-CN',
    'timeZone'=>'Asia/shanghai',
    //设置布局文件
    //'layout'=>false,
    //默认路由
    'defaultRoute'=>'user/login',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
           /* 'class'=>'yii\web\User',*/
            //指定实现认证接口的类
            'identityClass' => 'backend\models\User',
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
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
//            'suffix'=>'.html',
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
