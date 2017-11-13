<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Menu extends ActiveRecord{


    public function attributeLabels()
    {
        return [
            'name'=>'菜单名称',
            'sort'=>'排序',
            'menu_id'=>'上级菜单',
            'url'=>'路由',
        ];
    }
    public function rules()
    {
        return [
            [['name','menu_id','url','sort'],'required'],
            ['name','unique'],
            ['sort','integer']
        ];
    }

    /**
     * 一级菜单和二季菜单的关系
     */
    public function getChildren(){
        return $this->hasMany(self::className(),['menu_id'=>'id']);
    }
}