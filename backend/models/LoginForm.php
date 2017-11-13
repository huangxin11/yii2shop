<?php
namespace backend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $Remember_Password;
    public $code;
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'Remember_Password'=>'记住密码',
        ];
    }
    public function rules()
    {
        return [
            [['username','password','Remember_Password'],'required'],
            ['code','captcha']
        ];
    }
    public function login(){
        $user = User::findOne(['username'=>$this->username]);
        if ($user){
            if (\Yii::$app->security->validatePassword($this->password,$user->password_hash)){
                \Yii::$app->user->login($user,$this->Remember_Password?3600:0);
             return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
