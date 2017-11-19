<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class Order extends ActiveRecord{
    public function getGoods(){
        return $this->hasMany(OrderGoods::className(),['order_id'=>'id']);
    }
    public function rules()
    {
        return [
            [['name','province','city','area','address','tel','delivery_name','delivery_price','payment_name','total','status'],'required'],
        ];
    }
}