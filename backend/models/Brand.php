<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
    public $imgFile;
    public $code;
    public function attributeLabels()
    {
        return [
            'name'=>'品牌名称',
            'intro'=>'简介',
            'sort'=>'排序',
            'start'=>'状态',
        ];
    }
    public function rules()
    {
        return [
            [['name','intro','sort','start'],'required'],
            //上传文件规则验证
            ['imgFile','file','extensions'=>['jpg','gif','png']],
            //验证码
            ['code','captcha'],
        ];
    }
}
