<?php
namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    public $name;
    public $description;
    public $oldName;
    const SCENARIO_Add = 'add';
    const SCENARIO_EDIT = 'edit';
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
            //on表示只在该场景下生效
            ['name','validateName','on'=>self::SCENARIO_Add],
            ['name','validateUpdateName','on'=>self::SCENARIO_EDIT]
        ];
    }
    public function validateName(){
        $auth = \Yii::$app->authManager;
        if ($auth->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }
    public function validateUpdateName(){
        $auth = \Yii::$app->authManager;
        if($this->oldName != $this->name){
            if ($auth->getPermission($this->name)){
                $this->addError('name','权限已存在');
            };
        }
    }
    public function add(){
        $auth = \Yii::$app->authManager;
        $permission = $auth->createPermission($this->name);
        $permission->description = $this->description;
        return $auth->add($permission);
    }
    public function update($name){
        $auth = \Yii::$app->authManager;
        $newpermission = $auth->createPermission($this->name);
        $newpermission->description = $this->description;
        return $auth->update($name,$newpermission);
    }
}