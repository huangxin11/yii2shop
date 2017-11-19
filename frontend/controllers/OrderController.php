<?php
namespace frontend\controllers;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class OrderController extends Controller{
    public $enableCsrfValidation = false;
    public function actionFlow(){
        if (\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }
        //收货人信息
        $addresss = Address::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
        //商品信息
        $carts = Cart::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
        $carts = ArrayHelper::map($carts,'goods_id','amount');
        $models = Goods::find()->where(['in','id',array_keys($carts)])->all();
        return $this->render('flow2',['addresss'=>$addresss,'carts'=>$carts,'models'=>$models]);
    }
    /**
     * 显示订单信息
     */
    public function actionOrder(){
        $request = \Yii::$app->request;
        $model = new Order();
        if ($request->isPost){
        $data =\Yii::$app->request->post();
        //地址信息
        $address = Address::findOne(['id'=>$data['address']]);
        $model->name = $address->name;
        $model->province = $address->province;
        $model->city = $address->city;
        $model->area = $address->county;
        $model->address = $address->address;
        $model->tel = $address->phone;
        switch ($data['delivery']){
            case 1:
                $model->delivery_name = '普通快递送货上门';
                $model->delivery_price = 10;
                break;
            case 2:
                $model->delivery_name = '特快专递';
                $model->delivery_price = 40;
                break;
            case 3:
                $model->delivery_name = '加急快递送货上门';
                $model->delivery_price = 40;
                break;
            case 4:
                $model->delivery_name = '平邮';
                $model->delivery_price = 10;
                break;
        }
            switch ($data['pay']){
                case 1:
                    $model->payment_name = '货到付款';
                    break;
                case 2:
                    $model->payment_name = '在线支付';
                    break;
                case 3:
                    $model->payment_name = '上门自提';
                    break;
                case 4:
                    $model->payment_name = '邮局汇款';
                    break;
            }
            $model->member_id = \Yii::$app->user->identity->id;
            $carts = Cart::find()->where(['member_id'=>$model->member_id])->all();
            $carts = ArrayHelper::map($carts,'goods_id','amount');
            $goods = Goods::find()->where(['in','id',array_keys($carts)])->all();
           $total = 0;
            foreach ($goods as $good){
                $total += $good->shop_price*$carts[$good->id]+$model->delivery_price;
            }
            $model->total = $total;
            $model->status = 1;
            $model->create_time = time();
            //开启事务，在操作数据表之前开启
           $transaction = \Yii::$app->db->beginTransaction();//开启事务
            try{
                if ($model->save()){
                foreach ($goods as $good){
                    //判断商品库存是否足够
                    if ($carts[$good->id] > $good->stock){
                        throw new Exception($good->name.'商品库存不足');
                    }
                    $model2 = new OrderGoods();
                    $model2->order_id = $model->id;
                    $model2->goods_id = $good->id;
                    $model2->goods_name = $good->name;
                    $model2->logo = $good->logo;
                    $model2->price = $good->shop_price;
                    $model2->amount = $carts[$good->id];
                    $model2->total = $model2->price*$model2->amount;
                    if ($model2->save()){
                        echo 'success';
                    };
                    //减去商品库存
//                    $good->stock -= $model2->amount;
                    Goods::updateAllCounters(['stock'=>-$model2->amount],['id'=>$good->id]);
                }
                Cart::deleteAll('member_id='.\Yii::$app->user->id);

                }
                //提交事务
                $transaction->commit();
            }catch (Exception $e){
                //回滚
                $transaction->rollBack();
                //下单失败，
                echo $e->getMessage();exit;
            }

            }

    }
    public function actionView(){
        $orders = Order::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
        return $this->render('order',['orders'=>$orders]);
    }

    /**
     * 删除订单
     */
    public function actionDelete(){
        $id = $_POST['id'];
    }
}