<?php
namespace frontend\controllers;
use common\models\LoginForm;
use frontend\components\Sms;
use frontend\models\Member;
use yii\filters\AccessControl;
use yii\web\Controller;

class MemberController extends Controller{
    public $enableCsrfValidation = false;

    public function actionIndex(){
        return $this->redirect(['index/index']);
    }

    /**
     * @return string
     * 注册
     */
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new Member();
        if ($request->isPost){
        $model->load($request->post(),'');
            $redis = new \Redis();
            $redis->connect('127.0.0.1');
            $code = $redis->get('captcha_'.$model->tel);
        $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
        $model->status = 1;
            if ($model->validate() && ($model->captcha == $code) ){
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->created_at = time();
               $model->save(0);
               \Yii::$app->session->setFlash('success','注册成功');
               return $this->redirect(['member/login']);
            }else{
                var_dump($model->getErrors());die;
            }
        }
//        var_dump($_POST);die;
    }

    /**
     * @return string|\yii\web\Response
     * 登录
     */
    public function actionLogin(){
        $request = \Yii::$app->request;
        $model = new \frontend\models\LoginForm();
        if ($request->isPost){
            $model->load($request->post(),'');
//            var_dump($model->rememberMe);die;
            if ($model->validate()){
            if ($model->login()){
                \Yii::$app->session->setFlash('success','登录失败！用户名密码错误');
                return $this->redirect(['index/index']);
            }else{
                \Yii::$app->session->setFlash('success','登录失败！用户名密码错误');
                return $this->redirect(['member/login']);
            }
            }var_dump($model->getErrors());die;
        }
        return $this->render('login');
    }
    public function actionRegist(){
        return $this->render('regist');
    }

    /**
     * @param $username
     * 注销
     *     */
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['member/login']);
    }
    public function actionCheckName($username){
        $member = Member::findOne(['username'=>$username]);
        if ($member){
            echo 'false';
        }else{
            echo 'true';
        }
    }


    //ajax发送短信
    public function actionAjaxSms($phone){
        $code = rand(1000,9999);
        $response = Sms::sendSms(
            "Model鑫", // 短信签名
            "SMS_109430466", // 短信模板编号
            $phone, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$code,
            )/*,
        "123"   // 流水号,选填*/
        );
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis->set('captcha_'.$phone,$code,5*60);
        if ($response->Code == 'OK'){
            echo 'success';
        }else{
            echo 'false';
        }
    /*    echo "发送短信(sendSms)接口返回的结果:\n";
        print_r($response);*/
    }
    public function actionCheckSms(){
        $phone = $_POST['phone'];
        $sms = $_POST['sms'];
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $code = $redis->get('captcha_'.$phone);
        if ($code == $sms){
            echo 'true';
        }else{
            echo 'false';
        }
    }


    //测试阿里大于短信发送功能
    public function actionSms(){
        $response = Sms::sendSms(
            "Model鑫", // 短信签名
            "SMS_109430466", // 短信模板编号
            "18782451002", // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>rand(1000,9999),
            )/*,
        "123"   // 流水号,选填*/
        );
        echo "发送短信(sendSms)接口返回的结果:\n";
        print_r($response);

        //frontend\components\Sms ---> require '@frontend\components\Sms.php';
        //Aliyun\Core\Config;   ---> require  '@Aliyun\Core\Config.php';
    }
    /**
     * 配置
     */
//配置
// public function behaviors()
// {
//     return [
//         'acf'=>[ //简单存取过滤器 简单的权限控制
//             'class'=>AccessControl::className(),
//             'only'=>['delete','add','update','index','edit'],
//             'rules'=>[
//                 [ //允许登录用户访问  ?未登录  @已登录
//                     'allow'=>true,//允许
//                     'actions'=>['add','update','index','edit'], //操作
//                     'roles'=>['@'], //角色
//                 ],
//                 [//允许未登录用户访问
//                     'allow'=>true,
//                     'actions'=>['login','index'],
//                     'roles'=>['?']
//                 ],
//             ]
//
//         ]
//
//     ];
// }
}