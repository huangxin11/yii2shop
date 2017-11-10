<?php
namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    public $name;
    public $description;
    public function attributeLabels()
    {
        return [
            'name'=>'权限[路由]',
            'description'=>'描述',
        ];
    }
    public function rules()
    {
        return [
            [['name','description'],'required'],
        ];
    }
}