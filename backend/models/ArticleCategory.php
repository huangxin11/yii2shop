<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
    public $code;
    public function getArticle(){
        return $this->hasOne(Article::className(),['article_category_id'=>'id']);
    }
    public function attributeLabels()
    {
        return [
            'name'=>'分类名称',
            'intro'=>'简介',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required'],
            //验证码
            ['code','captcha'],
        ];
    }
}