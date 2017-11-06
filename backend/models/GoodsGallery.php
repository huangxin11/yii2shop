<?php
namespace backend\models;
use yii\db\ActiveRecord;

class GoodsGallery extends ActiveRecord{
    public function attributeLabels()
    {
        return [
            'path'=>'',
            'goods_id'=>'',
        ];
    }

    public function rules()
    {
        return [
            [['goods_id','path'],'required'],
        ];
    }
}
