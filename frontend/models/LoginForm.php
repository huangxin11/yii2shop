<?php
namespace frontend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $rememberMe;
    public $checkcode;
    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['rememberMe','boolean'],
            ['checkcode','captcha']

        ];
    }
    public function login(){
        $member = Member::findOne(['username'=>$this->username]);
//        var_dump($member->auth_key);die;
        if ($member){
            if (\Yii::$app->security->validatePassword($this->password,$member->password_hash)){
//                var_dump($this->rememberMe);die;
//                echo $this->rememberMe?3600:0;die;
                \Yii::$app->user->login($member,$this->rememberMe?3600:0);
                return true;
            }else{
                $this->addError('password','账号密码错误!');

            }
        }else{
            $this->addError('username','账号密码错误');

        }
    }
}
