<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class Cart extends ActiveRecord{
    public static $price = 0;
    public function getGoods(){
        return $this->hasOne(Goods::className(),['id'=>'goods_id']);
    }
    public function rules()
    {
        return [
            [['goods_id','amount'],'required'],
        ];
    }
//    public static function getPrice(){
//        $id = \Yii::$app->user->identity->id;
//        $prices = self::find()->where(['member_id'=>$id])->all();
//        foreach ($prices as $p){
//            self::$price += $p->amount * $p->goods->shop_price;
//        }
//        return self::$price;
//}
}
