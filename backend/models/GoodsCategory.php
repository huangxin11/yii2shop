<?php
namespace backend\models;
use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use backend\models\GoodsCategoryQuery;
class GoodsCategory extends ActiveRecord{
    public function attributeLabels()
    {
        return [
            'name'=>'商品分类名称',
            'parent_id'=>'上级节点',
            'intro'=>'简介',
        ];
    }
    public function rules()
    {
        return [
            [['name','parent_id','intro'],'required'],
        ];
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new GoodsCategoryQuery(get_called_class());
    }
    public static function getZtreeNodes(){
       return self::find()->select(['id','name','parent_id'])->asArray()->all();
    }
}
