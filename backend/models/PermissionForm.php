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
        //创建一个权限
        $permission = $auth->createPermission($this->name);
        //给权限赋值描述
        $permission->description = $this->description;
        //保存到数据库
        return $auth->add($permission);
    }
    public function update($name){
        $auth = \Yii::$app->authManager;
        $newpermission = $auth->createPermission($this->name);
        $newpermission->description = $this->description;
        //修改权限，第一个参数就旧权限名，第二个参数是新权限
        return $auth->update($name,$newpermission);
    }
}