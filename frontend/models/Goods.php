<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class Goods extends ActiveRecord{

    public function getBrand(){
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }
}