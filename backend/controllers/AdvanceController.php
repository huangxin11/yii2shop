<?php
namespace frontend\controllers;
use yii\web\Controller;

class AdvanceController extends Controller{
    //防止短信被刷的方案
    public function actionSms(){
        //设置短信发送间隔（1分钟只能发送一条，一小时只能发送7条）

        //验证码（在发送短信前输入验证码，验证通过发送短信）
    }
}
