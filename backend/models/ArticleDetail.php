<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleDetail extends ActiveRecord{
    public function attributeLabels()
    {
        return [
            'content'=>'内容'
        ];
    }
    public function rules()
    {
        return [
            [['content'],'required'],
        ];
    }
}
