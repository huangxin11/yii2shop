<?php
namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permissions;
    public function attributeLabels()
    {
        return [
            'name'=>'角色',
            'description'=>'描述',
            'permissions'=>'权限',
        ];
    }
    public function rules()
    {
        return [
            [
                ['name','description'],'required'],
            ['permissions','safe'],
        ];
    }
}
