<?php
namespace backend\models;
use yii\db\ActiveRecord;

class GoodsIntro extends ActiveRecord{
    public function attributeLabels()
    {
        return [
            'content'=>'简介'
        ];
    }
    public function rules()
    {
        return [
          ['content','required'],
        ];
    }
}